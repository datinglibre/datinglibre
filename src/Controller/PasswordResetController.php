<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetController extends AbstractController
{
    /**
     * @Route("/user/password", name="reset_password", condition="request.get('userId') == null || request.get('secret') == null")
     */
    public function password(Request $request, SessionInterface $session, UserService $userService): Response
    {
        $form = $this->createFormBuilder()
            ->add('email', TextType::class, ['required' => true])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userService->resetPassword($form->getData()['email']);

            $session->getFlashBag()->add('success', 'user.password_reset_email_sent');

            return $this->redirectToRoute('login');
        }

        return $this->render('user/password_reset.html.twig', [
            'passwordResetForm' => $form->createView(),
        ]);
    }
}
