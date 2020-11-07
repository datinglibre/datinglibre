<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity solely used as a projection
 */

/**
 * @ORM\Entity(readOnly=true)
 */
class ImageProjection
{
    /**
     * @ORM\Column(type="string", length=255, name="id")
     * @ORM\Id()
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $userId;

    /**
     * @ORM\Column(type="string")
     */
    private $secureUrl;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $secureUrlExpiry;

    /**
     * @ORM\Column(type="string")
     */
    private $state;

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getSecureUrl()
    {
        return $this->secureUrl;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    public function setSecureUrl($secureUrl)
    {
        $this->secureUrl = $secureUrl;
        return $this;
    }

    public function getSecureUrlExpiry(): DateTimeInterface
    {
        return $this->secureUrlExpiry;
    }

    public function setSecureUrlExpiry($secureUrlExpiry): void
    {
        $this->secureUrlExpiry = $secureUrlExpiry;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state): void
    {
        $this->state = $state;
    }
}
