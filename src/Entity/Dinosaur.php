<?php

namespace App\Entity;

class Dinosaur
{
    public const STATUS_HEALTHY = 'HEALTHY';
    public const STATUS_SICK = 'SICK';

    private string $name;
    private string $genus;
    private int $length;
    private string $enclosure;
    private string $health = self::STATUS_HEALTHY;

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

    public function getSpecification(): string
    {
        if ($this->length >= 10) {
            return 'Large';
        }

        if ($this->length >= 5) {
            return 'Medium';
        }

        return 'Small';
    }

    public function isAcceptingVisitors(): bool
    {
        if ($this->health === self::STATUS_HEALTHY) {
            return true;
        }

        return false;
    }

    public function setHealth(string $health): self
    {
        $this->health = $health;

        return $this;
    }
}
