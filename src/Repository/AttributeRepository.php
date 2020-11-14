<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Attribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Attribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attribute[]    findAll()
 * @method Attribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    public function getAttributesByNames(array $attributeNames): array
    {
        $attributes = [];

        foreach ($attributeNames as $attributeName) {
            $attribute = $this->findOneBy(['name' => trim($attributeName)]);

            if (null === $attribute) {
                throw new NoResultException();
            }

            $attributes[] = $attribute;
        }

        return $attributes;
    }
}
