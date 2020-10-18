<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 */
class MessageProjection
{
    /**
     * @ORM\Column(type="string", length=255, name="id")
     * @ORM\Id()
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $senderId;

    /**
     * @ORM\Column(type="string")
     */
    private $content;

    /**
     * @ORM\Column(type="string")
     */
    private $senderUsername;

    /**
     * @ORM\Column(type="string")
     */
    private $secureProfileImageUrl;


    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setSenderUsername($senderUsername)
    {
        $this->senderUsername = $senderUsername;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSenderId()
    {
        return $this->senderId;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getSenderUsername()
    {
        return $this->senderUsername;
    }

    public function getSecureProfileImageUrl() : ?string
    {
        return $this->secureProfileImageUrl;
    }

    public function setSecureProfileImageUrl(string $secureProfileImageUrl)
    {
        $this->secureProfileImageUrl = $secureProfileImageUrl;
        return $this;
    }
}
