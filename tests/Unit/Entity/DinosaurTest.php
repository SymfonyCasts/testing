<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    /** @dataProvider getDinoLengthSpecification */
    public function testDinoHasCorrectSpecificationFromLength(int $length, string $expectedSpecification): void
    {
        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: $length,
            enclosure: 'Paddock A'
        );

        self::assertSame($expectedSpecification, $dino->getSpecification());
    }

    public function getDinoLengthSpecification(): \Generator
    {
        yield '15 Meter Large Dino' => [15, 'Large'];
        yield '5 Meter Small Dino' => [5, 'Small'];
    }
}
