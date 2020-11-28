<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\UserAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Uid\Uuid;

/**
 * @method UserAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAttribute[]    findAll()
 * @method UserAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAttribute::class);
    }

    public function save(UserAttribute $userAttribute): UserAttribute
    {
        $this->getEntityManager()->persist($userAttribute);
        $this->getEntityManager()->flush();

        return $userAttribute;
    }

    public function deleteByCategory(Uuid $userId, Uuid $categoryId): void
    {
        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
DELETE FROM datinglibre.user_attributes ua
USING datinglibre.attributes a 
WHERE a.id = ua.attribute_id 
AND ua.user_id = :userId 
AND a.category_id = :categoryId
EOD, new ResultSetMapping());

        $query->setParameter('userId', $userId);
        $query->setParameter('categoryId', $categoryId);
        $query->execute();
    }

    public function getByUserAndCategory(Uuid $userId, string $categoryName): ?Attribute
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Entity\Attribute', 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'name', 'name');

        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
SELECT a.id, a.name FROM datinglibre.user_attributes ua
INNER JOIN datinglibre.attributes a ON a.id = ua.attribute_id
INNER JOIN datinglibre.categories c ON a.category_id = c.id
WHERE ua.user_id = :userId 
AND LOWER(c.name) = LOWER(:categoryName)
EOD, $rsm);

        $query->setParameter('userId', $userId);
        $query->setParameter('categoryName', $categoryName);

        return $query->getOneOrNullResult();
    }

    public function getAttributesByUser(string $userId)
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Entity\Attribute', 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'name', 'name');

        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
SELECT a.id, a.name FROM datinglibre.user_attributes ua 
INNER JOIN datinglibre.attributes a ON a.id = ua.attribute_id 
WHERE ua.user_id = :userId
EOD, $rsm);

        $query->setParameter('userId', $userId);

        return $query->getResult();
    }

    public function deleteByUser(Uuid $userId): void
    {
        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
DELETE FROM datinglibre.user_attributes ua
WHERE ua.user_id = :userId 
EOD, new ResultSetMapping());

        $query->setParameter('userId', $userId);
        $query->execute();
    }
}
