<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class MessageSendPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return "message_send";
    }

    public function sendMessage(string $message)
    {
        // sleep as otherwise tests run too quickly for messages
        // to display one after another
        sleep(1);
        $this->getElement('content')->setValue($message);
        $this->getElement('send')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'content' => '#message_form_content',
            'send' => '#message_form_submit'
        ]);
    }
}
