<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 * @ORM\Table(name="datinglibre.messages")
 * @ORM\HasLifecycleCallbacks
 */
class Message
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Thread", cascade={"persist"})
     * @JoinColumn(name="thread_id", referencedColumnName="id")
     */
    private Thread $thread;

    public function getId(): ?UuidInterface
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

    public function setThread(Thread $thread): Message
    {
        $this->thread = $thread;
        return $this;
    }

    public function getThread(): Thread
    {
        return $this->thread;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->sentTime = new DateTime('UTC');
    }
}
