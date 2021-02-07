<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Attribute;
use App\Entity\User;
use App\Entity\UserAttribute;
use App\Repository\AttributeRepository;
use App\Repository\UserAttributeRepository;

class UserAttributeService
{
    private UserAttributeRepository $userAttributeRepository;
    private AttributeRepository $attributeRepository;

    public function __construct(UserAttributeRepository $userAttributeRepository, AttributeRepository $attributeRepository)
    {
        $this->userAttributeRepository = $userAttributeRepository;
        $this->attributeRepository = $attributeRepository;
    }

    public function createUserAttributesByAttributeNames(User $user, array $attributeNames): void
    {
        $this->createUserAttributes($user, $this->attributeRepository->getAttributesByNames($attributeNames));
    }

    public function createUserAttributes(User $user, array $attributes): void
    {
        $this->userAttributeRepository->deleteByUser($user->getId());

        foreach ($attributes as $attribute) {
            $this->createUserAttribute($user, $attribute);
        }
    }

    public function createUserAttribute(User $user, Attribute $attribute): UserAttribute
    {
        $existingUserAttribute = $this->userAttributeRepository->findOneBy(['user' => $user,
            'attribute' => $attribute]);

        if (null !== $existingUserAttribute) {
            return $existingUserAttribute;
        }

        $userAttribute = new UserAttribute();
        $userAttribute->setUser($user);
        $userAttribute->setAttribute($attribute);

        return $this->userAttributeRepository->save($userAttribute);
    }

    public function getOneByCategoryName(User $user, string $categoryName): ?Attribute
    {
        return $this->userAttributeRepository->getOneByUserAndCategory($user->getId(), $categoryName);
    }

    public function getMultipleByCategoryName(User $user, string $categoryName): array
    {
        return $this->userAttributeRepository->getMultipleByUserAndCategory($user->getId(), $categoryName);
    }

    public function getAttributesByUser(string $userId): array
    {
        return $this->userAttributeRepository->getAttributesByUser($userId);
    }
}
