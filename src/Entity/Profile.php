<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @ORM\Table(name="datinglibre.profiles")
 * @ORM\Entity(repositoryClass="App\Repository\ProfileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Profile
{
    public const UNMODERATED = 'UNMODERATED';
    public const PASSED_MODERATION = 'PASSED_MODERATION';

    public function __construct()
    {
        $this->city = null;
    }

    /**
     * @ORM\Id()
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    private User $user;

    /**
     * @OneToOne(targetEntity="City")
     * @JoinColumn(name = "city_id", referencedColumnName = "id")
     */
    private ?City $city;

    /**
     * @ORM\Column(type="string", name="username")
     */
    private $username;

    /**
     * @ORM\Column(type="string", name="about")
     */
    private $about;

    /**
     * @ORM\Column(type="date", name="dob")
     */
    private $dob;

    /**
     * @ORM\Column(name="updated_at", type="datetimetz")
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="state", type="string")
     */
    private $state;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Profile
    {
        $this->user = $user;
        return $this;
    }

    public function setCity(City $city): Profile
    {
        $this->city = $city;
        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setUsername($username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setAbout($about): ?self
    {
        $this->about = $about;
        return $this;
    }

    public function getAbout()
    {
        return $this->about;
    }

    public function setDob($dob)
    {
        $this->dob = $dob;
        return $this;
    }

    public function getDob()
    {
        return $this->dob;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->updatedAt = new DateTime('UTC');
        $this->state = $this->state === null ? 'UNMODERATED' : $this->state;
    }
}
