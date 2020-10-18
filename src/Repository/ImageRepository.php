<?php

namespace App\Repository;

use App\Entity\Image;
use App\Entity\ImageProjection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Ramsey\Uuid\UuidInterface;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function save(Image $image): Image
    {
        $this->getEntityManager()->persist($image);
        $this->getEntityManager()->flush();
        return $image;
    }

    public function delete(Image $file): void
    {
        $this->getEntityManager()->remove($file);
        $this->getEntityManager()->flush();
    }

    public function findProjection(UuidInterface $userId, bool $isProfile): ?ImageProjection
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Entity\ImageProjection', 'ip');
        $rsm->addFieldResult('ip', 'id', 'id');
        $rsm->addFieldResult('ip', 'user_id', 'userId');
        $rsm->addFieldResult('ip', 'secure_url', 'secureUrl');
        $rsm->addFieldResult('ip', 'state', 'state');

        $query = $this->getEntityManager()->createNativeQuery(<<<EOD
SELECT id,
        user_id, 
        secure_url,
        state 
        FROM datinglibre.images 
        WHERE user_id = :userId AND is_profile = :isProfile
EOD, $rsm);
        $query->setParameter('userId', $userId);
        $query->setParameter('isProfile', $isProfile);

        return $query->getOneOrNullResult();
    }

    public function findUnmoderated(): ?ImageProjection
    {
        $rsm = new ResultSetMapping();

        $rsm->addEntityResult('App\Entity\ImageProjection', 'pip');
        $rsm->addFieldResult('pip', 'id', 'id');
        $rsm->addFieldResult('pip', 'user_id', 'userId');
        $rsm->addFieldResult('pip', 'secure_url', 'secureUrl');

        $query = $this->getEntityManager()->createNativeQuery(<<<EOD
SELECT id,
        secure_url, 
        user_id 
        FROM datinglibre.images 
        WHERE state = :state
        LIMIT 1
EOD, $rsm);

        $query->setParameter('state', Image::UNMODERATED);
        return $query->getOneOrNullResult();
    }
}
