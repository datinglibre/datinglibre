<?php

declare(strict_types=1);

namespace App\Form;

class RequirementsForm
{
    private $colors;
    private $shapes;

    public function getColors()
    {
        return $this->colors;
    }

    public function setColors($colors): void
    {
        $this->colors = $colors;
    }

    public function getShapes()
    {
        return $this->shapes;
    }

    public function setShapes($shapes): void
    {
        $this->shapes = $shapes;
    }
}
