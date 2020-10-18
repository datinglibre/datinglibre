<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BlockReason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlockReason|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockReason|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockReason[]    findAll()
 * @method BlockReason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockReasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockReason::class);
    }
}
