<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\Requirement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Requirement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requirement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requirement[]    findAll()
 * @method Requirement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequirementRepository extends ServiceEntityRepository
{
    private ResultSetMapping $attributeResultSetMapping;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requirement::class);

        $this->attributeResultSetMapping = new ResultSetMapping();
        $this->attributeResultSetMapping->addEntityResult('App\Entity\Attribute', 'a');
        $this->attributeResultSetMapping->addFieldResult('a', 'id', 'id');
        $this->attributeResultSetMapping->addFieldResult('a', 'name', 'name');
    }

    public function save(Requirement $requirement): Requirement
    {
        $this->getEntityManager()->persist($requirement);
        $this->getEntityManager()->flush();

        return $requirement;
    }

    public function deleteByUser(Uuid $userId): void
    {
        $query = $this->getEntityManager()->createNativeQuery(<<<EOD
DELETE FROM datinglibre.requirements r WHERE r.user_id = :userId
EOD, new ResultSetMapping());

        $query->setParameter('userId', $userId);
        $query->execute();
    }

    public function deleteByUserAndCategory(Uuid $userId, string $categoryName)
    {
        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
DELETE FROM datinglibre.requirements r
USING datinglibre.attributes AS a,
datinglibre.categories AS c 
WHERE r.user_id = :userId 
AND r.attribute_id = a.id
AND a.category_id = c.id
AND c.name = :categoryName
EOD, new ResultSetMapping());

        $query->setParameter('userId', $userId);
        $query->setParameter('categoryName', $categoryName);
        $query->execute();
    }

    private function getByUserAndCategoryQuery(Uuid $userId, string $categoryName)
    {
        $query = $this->getEntityManager()->createNativeQuery(<<<EOD
SELECT a.id, a.name FROM datinglibre.requirements r
INNER JOIN datinglibre.attributes a ON r.attribute_id = a.id
INNER JOIN datinglibre.categories c ON a.category_id = c.id
WHERE r.user_id = :userId 
AND c.name = :categoryName
EOD, $this->attributeResultSetMapping);

        $query->setParameter('userId', $userId);
        $query->setParameter('categoryName', $categoryName);

        return $query;
    }

    public function getMultipleByUserAndCategory(Uuid $userId, string $categoryName): array
    {
        return $this->getByUserAndCategoryQuery($userId, $categoryName)
            ->getResult();
    }

    public function getOneByUserAndCategory(Uuid $userId, string $categoryName): ?Attribute
    {
        return $this->getByUserAndCategoryQuery($userId, $categoryName)
            ->getOneOrNullResult();
    }
}
