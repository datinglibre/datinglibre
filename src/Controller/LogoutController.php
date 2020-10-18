<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\MessageRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController
{
    /**
     * @Route("/logout", name="logout")
     */
    public function matches()
    {
        throw new Exception('Specify logout in auth configuration');
    }
}
