<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 * @ORM\Table(name="datinglibre.tokens")
 * @ORM\HasLifecycleCallbacks
 */
class Token
{
    const USER = 'user';
    const SECRET = 'secret';
    const TYPE = 'type';
    const SIGNUP = 'SIGNUP';
    const PASSWORD_RESET = 'PASSWORD_RESET';

    /**
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @ORM\Column(type="uuid")
     */
    private $id;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @ORM\OneToOne(targetEntity="user")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    private $user;

    /**
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @ORM\Column(name="secret", type="string")
     */
    private $secret;

    /**
     * @ORM\Column(name="created_at", type="datetimetz")
     */
    private $createdAt;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function setSecret($secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new DateTime('UTC');
    }
}
