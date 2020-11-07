<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\User;
use App\Repository\ImageRepository;
use App\Service\ImageService;
use App\Service\UserService;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Common\Collections\Criteria;
use Webmozart\Assert\Assert;

class ImageContext extends RawMinkContext implements Context
{
    private UserService $userService;
    private ImageService $imageService;
    private ImageRepository $imageRepository;

    public function __construct(
        UserService $userService,
        ImageRepository $imageRepository,
        ImageService $imageService
    ) {
        $this->userService = $userService;
        $this->imageService = $imageService;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @BeforeScenario
     */
    public function clearFiles()
    {
        $images = $this->imageRepository->findAll();

        foreach ($images as $image) {
            $this->imageService->delete('images', $image);
        }
    }

    /**
     * @When I upload :file as the profile image for :email
     */
    public function iUpload(string $image, string $email): void
    {
        $content = file_get_contents($this->getMinkParameter('files_path')
            . DIRECTORY_SEPARATOR . $image);
        $user = $this->userService->create($email, 'password', true, []);
        $this->imageService->save($user->getId(), $content, 'jpg', true);
    }

    /**
     * @Then the image should be set as the profile image for :email
     */
    public function theImageShouldBeStored(string $email)
    {
        $user = $this->userService->findByEmail($email);
        Assert::notNull($user);
        $image = $this->imageService->findProfileImageProjection($user->getId());
        Assert::notNull($image);
        Assert::notNull($image->getSecureUrlExpiry());
        Assert::notNull($image->getSecureUrl());
    }

    /**
     * @Given the profile image for :email has expired
     */
    public function daysHavePassed(string $email): void
    {
        $user = $this->userService->findByEmail($email);
        Assert::notNull($user);
        $image = $this->imageRepository->findOneBy(['user' => $user->getId(), 'isProfile' => true]);
        Assert::notNull($image);

        $image->setSecureUrlExpiry($this->getDateInThePast(1));
        $this->imageRepository->save($image);
        Assert::true($this->hasExpiredProfileImage($user));
    }

    /**
     * @When the secure image refresh task has run
     */
    public function theSecureImageRefreshTaskHasRun(): void
    {
        $this->imageService->refreshSecureUrls();
    }

    /**
     * @Then generate a new expiry date for the profile image of :email
     */
    public function generateANewExpiryDate(string $email): void
    {
        $user = $this->userService->findByEmail($email);
        Assert::notNull($user);

        Assert::false($this->hasExpiredProfileImage($user));
    }

    private function getDateInThePast(int $minutes): DateTimeImmutable
    {
        $dateTime = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        return $dateTime->sub(new DateInterval(sprintf('PT%dM', $minutes)));
    }

    private function hasExpiredProfileImage(User $user): bool
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('user', $user->getId()))
            ->andWhere(Criteria::expr()->eq('isProfile', true))
            ->andWhere(Criteria::expr()
                ->lt('secureUrlExpiry', new DateTimeImmutable('now', new DateTimeZone('UTC'))));

        return $this->imageRepository->matching($criteria)->count() === 1;
    }
}
