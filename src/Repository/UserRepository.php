<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function deleteByEmail(string $email): void
    {
        $user = $this->findOneBy([User::EMAIL => $email]);

        if ($user === null) {
            return;
        }

        $this->_em->remove($user);
        $this->_em->flush();
    }

    public function delete(UuidInterface $userId): void
    {
        $user = $this->find($userId);

        if ($user === null) {
            return;
        }

        $this->_em->remove($user);
        $this->_em->flush();
    }

    public function updateCreatedAt(UuidInterface $userId, DateTimeImmutable $dateTime): void
    {
        $query = $this->getEntityManager()->createNativeQuery(<<<EOD
UPDATE datinglibre.users SET created_at = :createdAt WHERE id = :userId
EOD, new ResultSetMapping());

        $query->setParameter('userId', $userId);
        $query->setParameter('createdAt', $dateTime);

        $query->execute();
    }

    public function save(User $user): User
    {
        $this->_em->persist($user);
        $this->_em->flush();
        
        return $user;
    }
}
