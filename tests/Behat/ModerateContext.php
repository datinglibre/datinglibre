<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\ImageProjection;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ImageService;
use App\Tests\Behat\Page\ModerateProfileImagesPage;
use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

class ModerateContext implements Context
{
    private ModerateProfileImagesPage $moderateProfileImagesPage;
    private UserRepository $userRepository;
    private ImageService $imageService;

    public function __construct(
        ImageService $imageService,
        UserRepository $userRepository,
        ModerateProfileImagesPage $moderateProfileImagesPage
    ) {
        $this->moderateProfileImagesPage = $moderateProfileImagesPage;
        $this->userRepository = $userRepository;
        $this->imageService = $imageService;
    }

    /**
     * @Given I navigate to the moderate profile images page
     */
    public function iNavigateToTheModerateProfileImagesPage()
    {
        $this->moderateProfileImagesPage->open();
    }

    /**
     * @Then I should see the new profile image for :email
     */
    public function iShouldSeeTheNewProfileImageFor(string $email)
    {
        $user = $this->userRepository->findOneBy([User::EMAIL => $email]);
        Assert::notNull($user);

        /** @var ImageProjection $image */
        $image = $this->imageService->findUnmoderated();

        Assert::notNull($image);

        Assert::eq($image->getUserId(), $user->getId());
        Assert::notNull($image->getSecureUrl());
        Assert::notNull($image->getId());
    }

    /**
     * @Then I can see the new profile image for :email
     */
    public function iCanSeeTheNewProfileImageFor(string $email)
    {
        /** @var ImageProjection $image */
        $image = $this->imageService->findUnmoderated();

        Assert::notNull($image);

        $this->moderateProfileImagesPage->assertContains($image->getSecureUrl());
    }
}
