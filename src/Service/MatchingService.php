<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Attribute;
use App\Entity\UserAttribute;
use App\Entity\Requirement;
use App\Entity\User;
use App\Repository\AttributeRepository;
use App\Repository\UserAttributeRepository;
use App\Repository\RequirementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

class MatchingService
{
    private UserAttributeRepository $characteristicRepository;
    private RequirementRepository $requirementRepository;
    private AttributeRepository $attributeRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserAttributeRepository $characteristicRepository,
        RequirementRepository $requirementRepository,
        AttributeRepository $attributeRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->requirementRepository = $requirementRepository;
        $this->entityManager = $entityManager;
    }

    public function createCharacteristic(User $user, string $attributeName): UserAttribute
    {
        $attribute = $this->getAttributeByName($attributeName);

        $characteristic = new UserAttribute();
        $characteristic->setAttribute($attribute);
        $characteristic->setUser($user);
        $this->entityManager->persist($characteristic);
        $this->entityManager->flush();
        return $characteristic;
    }

    public function createRequirement(User $user, string $attributeName): Requirement
    {
        $attribute = $this->getAttributeByName($attributeName);

        $requirement = new Requirement();
        $requirement->setUser($user);
        $requirement->setAttribute($attribute);
        $this->entityManager->persist($requirement);
        $this->entityManager->flush();
        return $requirement;
    }

    public function getAttributeByName(string $attributeName): Attribute
    {
        $attribute = $this->attributeRepository->findOneBy(['name' => $attributeName]);

        if ($attribute == null) {
            throw new NoResultException();
        }
        return $attribute;
    }
}
