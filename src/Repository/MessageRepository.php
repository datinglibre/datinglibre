<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Thread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }
    
    public function findMessagesBetweenUsers(UuidInterface $userId, UuidInterface $participantId)
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Entity\MessageProjection', 'mp');
        $rsm->addFieldResult('mp', 'id', 'id');
        $rsm->addFieldResult('mp', 'sender_id', 'senderId');
        $rsm->addFieldResult('mp', 'sender_username', 'senderUsername');
        $rsm->addFieldResult('mp', 'content', 'content');
        $rsm->addFieldResult('mp', 'secure_profile_image_url', 'secureProfileImageUrl');

        $sql = <<<EOD
        SELECT m.id, 
        m.sender_id, 
        m.content,
        p.username AS sender_username,
        i.secure_url as secure_profile_image_url
        FROM datinglibre.messages m 
        INNER JOIN datinglibre.profiles p ON p.user_id = m.sender_id 
        LEFT JOIN datinglibre.images i ON p.user_id = i.user_id AND i.state = 'ACCEPTED' AND i.is_profile = TRUE 
        WHERE ((m.user_id = :userId AND m.sender_id = :participantId) 
           OR (m.user_id = :participantId AND m.sender_id = :userId))
        ORDER BY m.sent_time ASC   
EOD;
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('userId', $userId);
        $query->setParameter('participantId', $participantId);
        return $query->getResult();
    }


    public function save(Message $message): Message
    {
        $findExistingThreadIdSql =<<<EOD
        SELECT DISTINCT m.thread_id FROM datinglibre.messages m
        WHERE (m.user_id = :recipientId AND m.sender_id = :senderId)
        OR (m.user_id = :senderId AND m.sender_id = :recipientId);
EOD;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('thread_id', 'threadId');

        $query = $this->getEntityManager()->createNativeQuery($findExistingThreadIdSql, $rsm);
        $query->setParameter('senderId', $message->getUser()->getId());
        $query->setParameter('recipientId', $message->getSender()->getId());

        $result = $query->getOneOrNullResult();

        if (null === $result) {
            $message->setThreadId(Uuid::uuid4());
        } else {
            $message->setThreadId(UUID::fromString($result['threadId']));
        }

        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();

        return $message;
    }

    public function findLatestMessages(UuidInterface $userId)
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('App\Entity\LatestMessageProjection', 'lm');
        $rsm->addFieldResult('lm', 'current_recipient_id', 'currentRecipientId');
        $rsm->addFieldResult('lm', 'current_recipient_username', 'currentRecipientUsername');
        $rsm->addFieldResult('lm', 'current_recipient_profile_image_url', 'currentRecipientProfileImageUrl');
        $rsm->addFieldResult('lm', 'content', 'content');

        $sql =<<<EOD
        SELECT DISTINCT ON (m.thread_id)
        m.content AS content,
        m.sent_time,
        CAST(current_recipient_profile.user_id AS TEXT) AS current_recipient_id,
        current_recipient_profile.username AS current_recipient_username,
        current_recipient_profile_image.secure_url AS current_recipient_profile_image_url 
        FROM datinglibre.messages m 
        JOIN datinglibre.users sender ON m.sender_id = sender.id 
        JOIN datinglibre.users receiver ON m.user_id = receiver.id 
        JOIN datinglibre.profiles current_recipient_profile ON current_recipient_profile.user_id = CASE WHEN m.user_id = :userId THEN sender.id ELSE receiver.id END
        LEFT JOIN datinglibre.images current_recipient_profile_image ON current_recipient_profile.user_id = current_recipient_profile_image.user_id AND current_recipient_profile_image.state = 'ACCEPTED' AND current_recipient_profile_image.is_profile IS TRUE 
        JOIN datinglibre.profiles sender_profile ON m.sender_id = sender_profile.user_id
        JOIN datinglibre.profiles receiver_profile ON m.user_id = receiver_profile.user_id 
        WHERE (m.user_id = :userId OR m.sender_id = :userId) 
        AND NOT EXISTS (SELECT 1 FROM datinglibre.blocks b WHERE
            (b.user_id = :userId AND b.blocked_user_id = current_recipient_profile.user_id)
            OR
            (b.user_id = current_recipient_profile.user_id AND b.blocked_user_id = :userId))
        ORDER BY m.thread_id, m.sent_time DESC
EOD;
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('userId', $userId);

        return $query->getResult();
    }
}
