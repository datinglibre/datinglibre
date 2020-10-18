<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

class ModerateProfileImagesPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return "moderate_profile_images";
    }

    public function assertContains(string $content)
    {
        Assert::contains($this->getDriver()->getContent(), $content);
    }
}
