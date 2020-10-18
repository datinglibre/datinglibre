<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Attribute;
use App\Entity\User;
use App\Entity\UserAttribute;
use App\Repository\UserAttributeRepository;

class UserAttributeService
{
    private UserAttributeRepository $userAttributeRepository;

    public function __construct(UserAttributeRepository $userAttributeRepository)
    {
        $this->userAttributeRepository = $userAttributeRepository;
    }

    public function save(User $user, Attribute $attribute): UserAttribute
    {
        $existingUserAttribute = $this->userAttributeRepository->findOneBy(['user' => $user,
            'attribute' => $attribute]);

        if ($existingUserAttribute != null) {
            return $existingUserAttribute;
        }

        $this->userAttributeRepository->deleteByCategory($user->getId(), $attribute->getCategory()->getId());

        $userAttribute = new UserAttribute();
        $userAttribute->setUser($user);
        $userAttribute->setAttribute($attribute);

        return $this->userAttributeRepository->save($userAttribute);
    }

    public function getByCategory(User $user, string $categoryName): ?Attribute
    {
        return $this->userAttributeRepository->getByUserAndCategory($user->getId(), $categoryName);
    }

    public function getAttributesByUser(string $userId): array
    {
        return $this->userAttributeRepository->getAttributesByUser($userId);
    }
}
