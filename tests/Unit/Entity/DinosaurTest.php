<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    public function testDinoOver15MetersIsLarge(int $length, string $expectedSpecification): void
    {
        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: $length,
            enclosure: 'Paddock A'
        );

        self::assertSame($expectedSpecification, $dino->getSpecification(), 'This is supposed to be a small dino!');
    }

    public function getDinoLengthSpecification(): \Generator
    {
        yield [15, 'Large'];
        yield [5, 'Small'];
    }
}
