<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    /**
     * @Route("/token/{secret}", name="process_token", condition="request.get('userId') matches '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i' and request.get('type') matches '/signup/i'")
     */
    public function processConfirm(Request $request, UserService $userService, string $secret): Response
    {
        $userId = $request->get('userId');

        if ($userService->enable($userId, $secret)) {
            $this->addFlash('success', 'user.confirmed');
            return new RedirectResponse($this->generateUrl('login'));
        }

        $this->addFlash('danger', 'user.confirmation_failed');
        return new RedirectResponse($this->generateUrl('login'));
    }
}
