<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

class ProfileIndexPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return "profile_index";
    }

    public function assertDisplaysBlankProfile()
    {
        Assert::contains($this->getDriver()->getContent(), 'Unknown');
    }

    public function assertContains($content)
    {
        Assert::contains($this->getDriver()->getContent(), $content);
    }
}
