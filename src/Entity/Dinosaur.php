<?php

namespace App\Entity;

class Dinosaur
{
    public string $name;
    public string $genus;
    public int $length;
    public string $enclosure;

    public function __construct(string $name, string $genus, int $length, string $enclosure) {
        $this->name = $name;
        $this->genus = $genus;
        $this->length = $length;
        $this->enclosure = $enclosure;
    }

    public function getSpecification(): string
    {
        if ($this->length >= 10) {
            return 'Large';
        }

        return 'Small';
    }
}
