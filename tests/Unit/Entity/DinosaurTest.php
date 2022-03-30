<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    public function testDinoHasLength(): void
    {
        $dino = new Dinosaur();

        self::assertSame(0, $dino->length);
    }

    public function testDinoOver15MetersIsLarge(): void
    {
        $dino = new Dinosaur(20);

        self::assertSame(expected: 'Large', actual: $dino->getSpecification(), message: 'This is supposed to be a large dino!');

        $dino = new Dinosaur(10);

        self::assertSame(expected: 'Small', actual: $dino->getSpecification(), message: 'This is supposed to be a large dino!');
    }
}
