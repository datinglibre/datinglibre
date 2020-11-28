<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Attribute;
use App\Entity\Requirement;
use App\Entity\User;
use App\Repository\AttributeRepository;
use App\Repository\RequirementRepository;
use Symfony\Component\Uid\Uuid;

class RequirementService
{
    private RequirementRepository $requirementRepository;
    private AttributeRepository $attributeRepository;

    public function __construct(RequirementRepository $requirementRepository, AttributeRepository $attributeRepository)
    {
        $this->requirementRepository = $requirementRepository;
        $this->attributeRepository = $attributeRepository;
    }

    public function createRequirementsByAttributeNames(User $user, array $attributeNames): void
    {
        $this->createRequirements($user, $this->attributeRepository->getAttributesByNames($attributeNames));
    }

    public function createRequirementsInCategory(User $user, string $category, array $attributes)
    {
        $this->requirementRepository->deleteByUserAndCategory($user->getId(), $category);

        foreach ($attributes as $attribute) {
            $this->createRequirement($user, $attribute);
        }
    }

    public function createRequirements(User $user, array $attributes): void
    {
        $this->requirementRepository->deleteByUser($user->getId());

        foreach ($attributes as $attribute) {
            $this->createRequirement($user, $attribute);
        }
    }

    public function createRequirement(User $user, Attribute $attribute): Requirement
    {
        $requirement = new Requirement();
        $requirement->setUser($user);
        $requirement->setAttribute($attribute);
        $this->requirementRepository->save($requirement);
        return $requirement;
    }

    public function getMultipleByUserAndCategory(?Uuid $userId, string $categoryName): array
    {
        return $this->requirementRepository->getMultipleByUserAndCategory($userId, $categoryName);
    }

    public function getOneByUserAndCategory(?Uuid $userId, string $categoryName): ?Attribute
    {
        return $this->requirementRepository->getOneByUserAndCategory($userId, $categoryName);
    }
}
