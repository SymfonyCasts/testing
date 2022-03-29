<?php

namespace App\Entity;

class Dinosaur
{
    public int $length;

    public function __construct(int $length = 0)
    {
        $this->length = $length;
    }

    public function getSpecification(): string
    {
        if ($this->length >= 15) {
            return 'Large';
        }

        return 'Small';
    }
}
