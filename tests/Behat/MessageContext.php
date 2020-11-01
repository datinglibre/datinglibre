<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\LatestMessageProjection;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Service\UserService;
use App\Tests\Behat\Page\MatchesPage;
use App\Tests\Behat\Page\MessageSendPage;
use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

class MessageContext implements Context
{
    private MessageRepository $messageRepository;
    private UserService $userService;
    private MessageSendPage $messageSendPage;
    private MatchesPage $matchesPage;

    public function __construct(
        UserService $userService,
        MessageRepository $messageRepository,
        MessageSendPage $messagePage,
        MatchesPage $matchesPage
    ) {
        $this->userService = $userService;
        $this->messageRepository = $messageRepository;
        $this->messageSendPage = $messagePage;
        $this->matchesPage = $matchesPage;
    }

    /**
     * @When the user :sender sends the message :content to :recipient
     */
    public function theUserSendsTheMessageTo(
        string $sender,
        string $content,
        string $recipient
    ) {
        $sender = $this->userService->findByEmail($sender);
        $recipient = $this->userService->findByEmail($recipient);

        Assert::notNull($sender);
        Assert::notNull($recipient);

        $message = new Message();
        $message->setUser($recipient);
        $message->setSender($sender);
        $message->setContent($content);

        $this->messageRepository->save($message);
    }

    /**
     * @Then :recipient should have a new message with :content from :email
     */
    public function thisMessageShouldDisplayUnderANewMatchFor(
        string $recipient,
        string $content,
        string $sender
    ) {
        $recipient = $this->userService->findByEmail($recipient);
        Assert::notNull($recipient);

        $messages = $this->messageRepository->findLatestMessages($recipient->getId());
        $this->assertMessage($content, $sender, $messages);
    }

    private function assertMessage(string $content, string $sender, array $messages)
    {
        $found = false;
        /** @var LatestMessageProjection $message */
        foreach ($messages as $message) {
            if ($message->getContent() === $content) {
                $found = true;
            }
        }

        Assert::true($found, 'Did not find message with ' . $content . " " . $sender);
    }

    /**
     * @Then :email should have no messages
     */
    public function shouldHaveNoMessages(string $email)
    {
        $user = $this->userService->findByEmail($email);
        $messages = $this->messageRepository->findLatestMessages($user->getId());
        Assert::true(empty($messages));
    }

    /**
     * @Given I navigate to message user :email
     */
    public function iNavigateToMessageUser(string $email)
    {
        $user = $this->userService->findByEmail($email);
        Assert::notNull($user);
        $this->messageSendPage->open(['userId' => $user->getId()]);
    }

    /**
     * @Given I send the message :message
     */
    public function iSendTheMessage(string $message)
    {
        $this->messageSendPage->sendMessage($message);
    }

    /**
     * @Given I navigate to the matches page
     */
    public function iNavigateToTheMatches()
    {
        $this->matchesPage->open();
    }

    /**
     * @Given the profile image is displayed
     */
    public function theProfileImageIsDisplayed()
    {
        $this->matchesPage->assertProfileImageDisplayed();
    }

    /**
     * @Then the anonymous profile image should be displayed
     */
    public function theAnonymousProfileImageShouldBeDisplayed()
    {
        $this->matchesPage->assertAnonymousProfileImageDisplayed();
    }
}
