<?php

namespace App\Repository;

use App\Entity\Token;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function save(Token $token): Token
    {
        $this->_em->persist($token);
        $this->_em->flush();

        return $token;
    }

    public function deleteByUserIdAndType(Uuid $userId, string $type): void
    {
        $query = $this->getEntityManager()->createNativeQuery(<<<EOD
        DELETE FROM datinglibre.tokens where user_id = :userId AND type = :type
EOD, new ResultSetMapping());
        $query->setParameter('userId', $userId);
        $query->setParameter('type', $type);

        $query->execute();
    }
}
