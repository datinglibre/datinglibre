<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\ProfileProjection;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use App\Service\ImageService;
use App\Service\ProfileService;
use App\Tests\Behat\Page\ProfileImagePage;
use Behat\MinkExtension\Context\RawMinkContext;
use Webmozart\Assert\Assert;

class ProfileImageContext extends RawMinkContext
{
    private ProfileImagePage $profileImagePage;
    private ImageService $imageService;
    private ProfileService $profileService;
    private ProfileRepository $profileRepository;
    private UserRepository $userRepository;

    public function __construct(
        ProfileImagePage $profileImagePage,
        ProfileService $profileService,
        ProfileRepository $profileRepository,
        UserRepository $userRepository,
        ImageService $imageService
    ) {
        $this->profileImagePage = $profileImagePage;
        $this->profileService = $profileService;
        $this->profileRepository = $profileRepository;
        $this->userRepository = $userRepository;
        $this->imageService = $imageService;
    }

    /**
     * @Given I am on the profile image page
     */
    public function iAmOnTheProfileImagePage()
    {
        $this->profileImagePage->open();
    }

    /**
     * @Given I wait until the profile image has uploaded
     */
    public function iWaitUntilTheProfileImageHasUploaded()
    {
        $this->profileImagePage->waitUntilImageUploaded();
    }

    /**
     * @Given the user :email has uploaded a profile image
     */
    public function theUserHasUploadedAProfileImage(string $email)
    {
        $user = $this->userRepository->findOneBy([User::EMAIL => $email]);
        Assert::notNull($user);

        $profileImage =
            file_get_contents($this->getMinkParameter('files_path') . DIRECTORY_SEPARATOR . 'cat.jpg');

        $image = $this->imageService->save($user->getId(), $profileImage, 'jpg', true);
        Assert::notNull($image);
    }

    /**
     * @Then the user :email should not be able to see the profile image of :otherEmail
     */
    public function theUserShouldNotBeAbleToSeeTheProfileImageOf(string $currentUserEmail, string $otherUserEmail)
    {
        $profileProjection = $this->findProfileProjectionByCurrentUser($currentUserEmail, $otherUserEmail);

        Assert::null($profileProjection->getImageUrl());
        Assert::false($profileProjection->isImagePresent());
    }

    /**
     * @Then the user :email should be able to see the profile image of :anotherEmail
     */
    public function theUserShouldBeAbleToSeeTheProfileImageOf(string $currentUserEmail, $otherUserEmail)
    {
        $profileProjection = $this->findProfileProjectionByCurrentUser($currentUserEmail, $otherUserEmail);

        Assert::true($profileProjection->isImagePresent());
        Assert::notNull($profileProjection->getImageUrl());
    }

    /**
     * @Given the profile image for :email has failed moderation
     */
    public function theProfileImageForHasFailedModeration(string $email)
    {
        $this->imageService->reject($this->getProfileImageId($email));
    }

    /**
     * @Given the profile image for :email has passed moderation
     */
    public function theProfileImageForHasPassedModeration(string $email)
    {
        $this->imageService->accept($this->getProfileImageId($email));
    }

    public function getProfileImageId(string $email): string
    {
        $user = $this->userRepository->findOneBy([User::EMAIL => $email]);
        Assert::notNull($user);

        $profileImageProjection = $this->imageService->findProfileImageProjection($user->getId());
        Assert::notNull($profileImageProjection);
        return $profileImageProjection->getId();
    }

    public function findProfileProjectionByCurrentUser(string $email, string $otherEmail): ProfileProjection
    {
        $user = $this->userRepository->findOneBy([User::EMAIL => $email]);
        Assert::notNull($user);

        $anotherUser = $this->userRepository->findOneBy([User::EMAIL => $otherEmail]);
        Assert::notNull($anotherUser);

        /** @var ProfileProjection $profileProjection */
        $profileProjection =
            $this->profileRepository->findProjectionByCurrentUser($user->getId(), $anotherUser->getId());

        return $profileProjection;
    }
}
