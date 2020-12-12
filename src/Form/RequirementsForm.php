<?php

declare(strict_types=1);

namespace App\Form;

class RequirementsForm
{
    private $sexes;

    public function getSexes()
    {
        return $this->sexes;
    }

    public function setSexes($sexes): void
    {
        $this->sexes = $sexes;
    }
}
