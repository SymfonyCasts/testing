<?php

namespace App\Entity;

class Dinosaur
{
    public string $name;
    public string $genus;
    public int $length;
    public string $enclosure;
    public bool $healthy;

    public function __construct(string $name, string $genus, int $length, string $enclosure, bool $healthy = true) {
        $this->name = $name;
        $this->genus = $genus;
        $this->length = $length;
        $this->enclosure = $enclosure;
        $this->healthy = $healthy;
    }

    public function getSpecification(): string
    {
        if ($this->length >= 10) {
            return 'Large';
        }

        return 'Small';
    }
}
