<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Requirement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @method Requirement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requirement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requirement[]    findAll()
 * @method Requirement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequirementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requirement::class);
    }

    public function save(Requirement $requirement): Requirement
    {
        $this->getEntityManager()->persist($requirement);
        $this->getEntityManager()->flush();

        return $requirement;
    }

    public function deleteByUser(UuidInterface $userId): void
    {
        $query = $this->getEntityManager()->createNativeQuery(<<<EOD
DELETE FROM datinglibre.requirements r WHERE r.user_id = :userId
EOD, new ResultSetMapping());

        $query->setParameter('userId', $userId);
        $query->execute();
    }

    public function getByUserAndCategory(?UuidInterface $userId, string $categoryName): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Entity\Attribute', 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'name', 'name');

        $query = $this->getEntityManager()->createNativeQuery(<<<EOD
SELECT a.id, a.name FROM datinglibre.requirements r
INNER JOIN datinglibre.attributes a ON r.attribute_id = a.id
INNER JOIN datinglibre.categories c ON a.category_id = c.id
WHERE r.user_id = :userId 
AND c.name = :categoryName
EOD, $rsm);

        $query->setParameter('userId', $userId);
        $query->setParameter('categoryName', $categoryName);
        return $query->getResult();
    }
}
