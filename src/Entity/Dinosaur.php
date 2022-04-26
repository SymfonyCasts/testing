<?php

namespace App\Entity;

class Dinosaur
{
    private string $name;
    private string $genus;
    private int $length;
    private string $enclosure;

    public function __construct(string $name, string $genus, int $length, string $enclosure)
    {
        $this->name = $name;
        $this->genus = $genus;
        $this->length = $length;
        $this->enclosure = $enclosure;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGenus(): string
    {
        return $this->genus;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getEnclosure(): string
    {
        return $this->enclosure;
    }
}
