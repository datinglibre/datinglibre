<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

class BlockPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return 'block_user';
    }

    public function assertContains(string $value): void
    {
        Assert::contains($this->getSession()->getPage()->getContent(), $value);
    }
}