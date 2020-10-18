<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FilterRepository")
 * @ORM\Table(name="datinglibre.filters")
 */
class Filter
{
    /**
     * @ORM\Id()
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    private User $user;

    /**
     * @ORM\OneToOne(targetEntity="Region")
     * @JoinColumn(name = "region_id", referencedColumnName = "id")
     */
    private ?Region $region;

    /**
     * @ORM\Column(type="integer")
     */
    private $distance = 100_000;

    /**
     * @ORM\Column(type="integer")
     */
    private $minAge = 18;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxAge = 100;

    public function __construct()
    {
        $this->region = null;
    }

    public function setUser(User $user): Filter
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    public function setMinAge(int $minAge): self
    {
        $this->minAge = $minAge;
        return $this;
    }

    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    public function setMaxAge(?int $maxAge): self
    {
        $this->maxAge= $maxAge;
        return $this;
    }
}
