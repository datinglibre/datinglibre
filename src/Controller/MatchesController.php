<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MatchesController extends AbstractController
{
    private MessageRepository $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * @Route("/matches", name="matches_index")
     */
    public function matches()
    {
        return $this->render('matches/index.html.twig', [
            'matches' => $this->messageRepository->findLatestMessages($this->getUser()->getId()),
            'controller_name' => 'MatchesController'
        ]);
    }
}
