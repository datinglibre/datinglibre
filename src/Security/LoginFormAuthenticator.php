<?php

/**
 * This file is based on https://symfony.com/doc/current/security/form_login_setup.html
 *
 * It licenced under a Creative Commons BY-SA 3.0 licence
 *
 * https://creativecommons.org/licenses/by-sa/3.0/
 *
 * The following changes have been made:
 * - Use CustomUserMessageAuthenticationException to supply custom translation.
 * - Throw CustomUserMessageAuthenticationException if user is null,
 *   to not leak who is registered on the site.
 */

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    const USER_INCORRECT_CREDENTIALS_KEY = 'user.incorrect_credentials';
    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordEncoderInterface $passwordEncoder;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function supports(Request $request)
    {
        return 'login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy([User::EMAIL => trim(strtolower($credentials['email'])), User::ENABLED => true]);

        if (!$user) {
            // This is the same message as credential failure,
            // to not leak who is registered on the site
            throw new CustomUserMessageAuthenticationException(self::USER_INCORRECT_CREDENTIALS_KEY);
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            throw new CustomUserMessageAuthenticationException(self::USER_INCORRECT_CREDENTIALS_KEY);
        } else {
            return true;
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        if ($this->authorizationChecker->isGranted(User::MODERATOR)) {
            return new RedirectResponse($this->urlGenerator->generate('moderate_profile_images'));
        }

        return new RedirectResponse($this->urlGenerator->generate('search'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('login');
    }
}
