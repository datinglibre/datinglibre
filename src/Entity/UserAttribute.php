<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserAttributeRepository")
 * @ORM\Table(name="datinglibre.user_attributes")

 */
class UserAttribute
{
    /**
     * @Id()
     * @OneToOne(targetEntity="Attribute")
     * @JoinColumn(name = "attribute_id", referencedColumnName = "id")
     */
    private $attribute;

    /**
     * @Id()
     * @OneToOne(targetEntity="user")
     * @JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    private $user;

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }
}
