<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Profile;
use App\Entity\ProfileProjection;
use App\Repository\ProfileRepository;
use Exception;
use Symfony\Component\Uid\Uuid;

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

    public function findByLocation(
        Uuid $userId,
        ?int $distance,
        ?Uuid $regionId,
        int $minAge,
        int $maxAge,
        int $previous,
        int $next,
        $limit
    ): array {
        $profile = $this->profileRepository->find($userId);
        $city = $profile->getCity();

        if ($minAge > $maxAge) {
            throw new Exception('Minimum greater than maximum age');
        }

        return $this->profileRepository->findByLocation(
            $userId,
            $city->getLatitude(),
            $city->getLongitude(),
            $distance,
            $regionId,
            $minAge,
            $maxAge,
            $previous,
            $next,
            $limit
        );
    }

    public function find($id): ?Profile
    {
        return $this->profileRepository->find($id);
    }

    public function findProjectionByCurrentUser(Uuid $currentUserId, Uuid $userId): ?ProfileProjection
    {
        return $this->profileRepository->findProjectionByCurrentUser($currentUserId, $userId);
    }

    public function findProjection(Uuid $userId): ProfileProjection
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

    public function delete(Uuid $userId)
    {
        $profile = $this->profileRepository->find($userId);

        if ($profile === null) {
            return;
        }

        $this->imageService->deleteByUserId($userId);
        $this->profileRepository->delete($profile);
    }
}
