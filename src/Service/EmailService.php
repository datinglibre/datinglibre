<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Email;
use App\Entity\User;
use App\Repository\EmailRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Message;

class EmailService
{
    private MailerInterface $mailer;
    private EmailRepository $emailRepository;

    public function __construct(MailerInterface $mailer, EmailRepository $emailRepository)
    {
        $this->mailer = $mailer;
        $this->emailRepository = $emailRepository;
    }

    public function send(Message $message, User $user, string $type)
    {
        $this->mailer->send($message);

        $email = new Email();
        $email->setType($type);
        $email->setUser($user);
        $this->emailRepository->save($email);
    }
}
