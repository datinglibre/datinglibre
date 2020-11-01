<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

class MatchesPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return "matches_index";
    }

    public function assertProfileImageDisplayed()
    {
        Assert::contains($this->getSession()->getPage()->getContent(), 'X-Amz-Expires');
    }

    public function assertAnonymousProfileImageDisplayed()
    {
        $pageContent = $this->getSession()->getPage()->getContent();

        Assert::contains($pageContent, 'profile.jpg');
        Assert::notContains($pageContent, 'X-Amz-Expires');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
        ]);
    }
}
