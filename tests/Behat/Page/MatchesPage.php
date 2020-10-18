<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class MatchesPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return "matches_index";
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
        ]);
    }
}
