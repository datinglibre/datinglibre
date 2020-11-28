<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 * @ORM\Table(name="datinglibre.messages")
 * @ORM\HasLifecycleCallbacks
 */
class Message
{
    /**
     * @var Uuid
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @OneToOne(targetEntity="user")
     * @JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    private User $user;

    /**
     * @ORM\OneToOne(targetEntity="user")
     * @JoinColumn(name = "sender_id", referencedColumnName = "id")
     */
    private User $sender;

    /**
     * @ORM\Column(type="string", length=10000)
     */
    private $content;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $sentTime;

    /**
     * @ORM\Column
     */
    private Uuid $threadId;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSentTime(): ?DateTimeInterface
    {
        return $this->sentTime;
    }

    public function setSentTime(DateTimeInterface $sentTime): self
    {
        $this->sentTime = $sentTime;

        return $this;
    }

    public function setThreadId(Uuid $threadId): Message
    {
        $this->threadId = $threadId;
        return $this;
    }

    public function getThreadId(): Uuid
    {
        return $this->threadId;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->sentTime = new DateTime('UTC');
    }
}
