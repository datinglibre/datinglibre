<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Service\ImageService;
use App\Service\UserService;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Webmozart\Assert\Assert;

class ImageContext extends RawMinkContext implements Context
{
    private UserService $userService;
    private ImageService $imageService;
    private ImageRepository $imageRepository;
    private ?Image $image;

    public function __construct(
        UserService $userService,
        ImageRepository $imageRepository,
        ImageService $imageService
    ) {
        $this->userService = $userService;
        $this->imageService = $imageService;
        $this->imageRepository = $imageRepository;
        $this->image = null;
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
     * @When I upload :file
     */
    public function iUpload(string $image)
    {
        $content = file_get_contents($this->getMinkParameter('files_path')
            . DIRECTORY_SEPARATOR . $image);
        $user = $this->userService->create('imageuser@exampl.com', 'password', true, []);
        $this->image = $this->imageService->save($user->getId(), $content, 'jpg', true);
    }

    /**
     * @Then the image should be stored
     */
    public function theImageShouldBeStored()
    {
        Assert::notNull($this->image->getSecureUrlExpiry());
        Assert::notNull($this->image->getSecureUrl());
    }
}
