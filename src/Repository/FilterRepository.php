<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Filter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FilterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filter::class);
    }

    public function save(Filter $search): Filter
    {
        $this->getEntityManager()->persist($search);
        $this->getEntityManager()->flush();

        return $search;
    }
}
