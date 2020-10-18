<?php

declare(strict_types=1);

namespace App\Form;

class MessageForm
{
    private $content;

    public function __construct()
    {
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }
}
