<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\PasswordResetFormType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use function strtolower;

class PasswordResetController extends AbstractController
{
    /**
     * @Route("/user/password", name="reset_password", condition="request.get('userId') == null || request.get('secret') == null")
     */
    public function password(Request $request, SessionInterface $session, UserService $userService): Response
    {
        $passwordResetFormType = $this->createForm(PasswordResetFormType::class);
        $passwordResetFormType->handleRequest($request);

        if ($passwordResetFormType->isSubmitted() && $passwordResetFormType->isValid()) {
            $userService->resetPassword(strtolower($passwordResetFormType->getData()['email']));

            $session->getFlashBag()->add('success', 'user.password_reset_email_sent');

            return $this->redirectToRoute('login');
        }

        return $this->render('user/password_reset.html.twig', [
            'passwordResetForm' => $passwordResetFormType->createView(),
        ]);
    }
}
