<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Profile;
use App\Entity\ProfileProjection;
use App\Repository\ProfileRepository;
use Exception;
use Ramsey\Uuid\UuidInterface;

class ProfileService
{
    private ProfileRepository $profileRepository;
    private ImageService $imageService;

    public function __construct(
        ProfileRepository $profileRepository,
        ImageService $imageService
    ) {
        $this->profileRepository = $profileRepository;
        $this->imageService = $imageService;
    }

    public function findByRadius(
        UuidInterface $userId,
        int $radius,
        int $minAge,
        int $maxAge,
        bool $previous,
        int $sortId,
        $limit
    ): array {
        $profile = $this->profileRepository->find($userId);
        $city = $profile->getCity();

        if ($minAge > $maxAge) {
            throw new Exception('Minimum greater than maximum age');
        }

        return $this->profileRepository->findByRadius(
            $userId,
            $city->getLatitude(),
            $city->getLongitude(),
            $radius,
            $minAge,
            $maxAge,
            $previous,
            $sortId,
            $limit
        );
    }

    public function find($id): ?Profile
    {
        return $this->profileRepository->find($id);
    }

    public function findProjection(UuidInterface $userId): ProfileProjection
    {
        $profileProjection = $this->profileRepository->findProjection($userId);

        if ($profileProjection == null) {
            $profileProjection = new ProfileProjection();
            // see if the user has uploaded a profile image
            // before completing a profile
            $imageProjection = $this->imageService->findProfileImageProjection($userId);
            if ($imageProjection == null) {
                return $profileProjection;
            } else {
                $profileProjection->setImageState($imageProjection->getState());
                $profileProjection->setImageUrl($imageProjection->getSecureUrl());
                return $profileProjection;
            }
        }

        return $profileProjection;
    }

    public function delete(UuidInterface $userId)
    {
        $profile = $this->profileRepository->find($userId);

        if ($profile === null) {
            return;
        }

        $this->imageService->deleteByUserId($userId);
        $this->profileRepository->delete($profile);
    }
}
