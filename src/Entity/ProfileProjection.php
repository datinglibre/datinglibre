<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 */
class ProfileProjection
{
    /**
     * Entity solely used as a projection
     */

    /**
     * @ORM\Column(type="string", length=255, name="id")
     * @ORM\Id()
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $username;

    /**
     * @ORM\Column(type="integer")
     */
    private $age;

    /**
     * @ORM\Column(type="string")
     */
    private $about;

    /**
     * @ORM\Column(type="string", name="city_name")
     */
    private $cityName;

    /**
     * @ORM\Column(type="string", name="region_name")
     */
    private $regionName;

    /**
     * @ORM\Column(type="datetimetz", name="last_login")
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="integer")
     */
    private $sortId;

    /**
     * @ORM\Column(type="string")
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="string")
     */
    private $imageState;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setUsername($username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setAbout($about): self
    {
        $this->about = $about;
        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setCityName($cityName)
    {
        $this->cityName = $cityName;
        return $this;
    }

    public function getCityName()
    {
        return $this->cityName;
    }

    public function setRegionName($regionName)
    {
        $this->regionName = $regionName;
        return $this;
    }

    public function getRegionName()
    {
        return $this->regionName;
    }

    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }


    public function getAge()
    {
        return $this->age;
    }

    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
        return $this;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setSortId($sortId)
    {
        $this->sortId = $sortId;
        return $this;
    }

    public function getSortId()
    {
        return $this->sortId;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function setImageState($imageState)
    {
        $this->imageState = $imageState;
        return $this;
    }

    public function getImageState()
    {
        return $this->imageState;
    }

    public function isImagePresent()
    {
        return null !== $this->getImageState();
    }

    public function isImageUnmoderated()
    {
        return Image::UNMODERATED === $this->getImageState();
    }

    public function isImageRejected()
    {
        return Image::REJECTED === $this->getImageState();
    }
}
