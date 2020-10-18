<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 */
class LatestMessageProjection
{
    /**
     * Entity solely used as a projection
     */

    /**
     * @ORM\Column(type="string", length=255, name="id")
     * @ORM\Id()
     */
    private string $currentRecipientId;

    /**
     * @ORM\Column(type="string")
     */
    private string $currentRecipientUsername;

    /**
     * @ORM\Column(type="string")
     */
    private string $content;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $currentRecipientProfileImageUrl;

    public function setCurrentRecipientId(string $currentRecipientId)
    : LatestMessageProjection
    {
        $this->currentRecipientId = $currentRecipientId;
        return $this;
    }

    public function setCurrentRecipientUsername(string $currentRecipientUsername)
    : LatestMessageProjection
    {
        $this->currentRecipientUsername = $currentRecipientUsername;
        return $this;
    }

    public function setContent(string $content): LatestMessageProjection
    {
        $this->content = $content;
        return $this;
    }

    public function setCurrentRecipientProfileImageUrl(string $currentRecipientProfileImageUrl)
    : LatestMessageProjection
    {
        $this->currentRecipientProfileImageUrl = $currentRecipientProfileImageUrl;
        return $this;
    }

    public function getCurrentRecipientId(): string
    {
        return $this->currentRecipientId;
    }

    public function getCurrentRecipientUsername(): string
    {
        return $this->currentRecipientUsername;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCurrentRecipientProfileImageUrl(): ?string
    {
        return $this->currentRecipientProfileImageUrl;
    }
}
