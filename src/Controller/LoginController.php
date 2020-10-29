<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/", name="login")
     */
    public function login(
        bool $isDemo,
        AuthenticationUtils $authenticationUtils,
        AuthorizationCheckerInterface $authChecker
    ): Response
    {
        if ($authChecker->isGranted(User::MODERATOR)) {
            return $this->redirectToRoute('moderate_profile_images');
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('search');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'isDemo' => $isDemo
        ]);
    }
}
