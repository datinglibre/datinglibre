<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageForm;
use App\Form\MessageFormType;
use App\Repository\MessageRepository;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageSendController extends AbstractController
{
    private ProfileRepository $profileRepository;
    private MessageRepository $messageRepository;
    private UserRepository $userRepository;

    public function __construct(
        ProfileRepository $profileRepository,
        UserRepository $userRepository,
        MessageRepository $messageRepository
    ) {
        $this->profileRepository = $profileRepository;
        $this->messageRepository = $messageRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/message/{userId}", name="message_send")
     */
    public function message(Request $request, UuidInterface $userId)
    {
        $sender = $this->userRepository->find($this->getUser()->getId());
        $recipient = $this->userRepository->find($userId);

        if ($sender === null || $recipient === null) {
            throw $this->createNotFoundException();
        }

        $recipientProfile = $this->profileRepository->findProjectionByCurrentUser(
            $sender->getId(),
            $userId
        );

        if ($recipientProfile === null) {
            throw $this->createNotFoundException();
        }

        $messageForm = new MessageForm();
        $messageFormType = $this->createForm(MessageFormType::class, $messageForm);
        $messageFormType->handleRequest($request);

        if ($messageFormType->isSubmitted() && $messageFormType->isValid()) {
            $message = new Message();
            $message->setContent($messageFormType->getData()->getContent());
            $message->setSender($sender);
            $message->setUser($recipient);

            $this->messageRepository->save($message);

            $this->addFlash('success', 'message.sent');
            return new RedirectResponse($this->generateUrl(
                'message_send',
                ['userId' => $userId]
            ));
        }

        return $this->render('message/send.html.twig', [
            'messages' => $this->messageRepository->findMessagesBetweenUsers(
                $sender->getId(),
                $recipient->getId()
            ),
            'profile' => $recipientProfile,
            'messageForm' => $messageFormType->createView(),
            'controller_name' => 'MessageSendController'
        ]);
    }
}
