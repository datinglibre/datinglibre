<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Attribute;
use App\Entity\Requirement;
use App\Entity\User;
use App\Repository\AttributeRepository;
use App\Repository\RequirementRepository;
use Ramsey\Uuid\UuidInterface;

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

    public function getByUserAndCategory(?UuidInterface $userId, string $categoryName): array
    {
        return $this->requirementRepository->getByUserAndCategory($userId, $categoryName);
    }
}
