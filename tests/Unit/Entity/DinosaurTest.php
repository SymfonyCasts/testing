<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    public function testDinosaur(): void
    {
        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 15,
            enclosure: 'Paddock A',
        );

        self::assertSame('Big Eaty', $dino->getName());
        self::assertSame('Tyrannosaurus', $dino->getGenus());
        self::assertSame(15, $dino->getLength(), 'Dino length getter is not the same as the constructor');
        self::assertSame('Paddock A', $dino->getEnclosure());
    }

    public function testDinoOver15MetersIsLarge(): void
    {
        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 15,
            enclosure: 'Paddock A',
        );

        self::assertSame('Large', $dino->getSpecification(), 'This is supposed to be a large Dinosaur');

        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 7,
            enclosure: 'Paddock A',
        );

        self::assertSame('Medium', $dino->getSpecification(), 'This is supposed to be a medium Dinosaur');

        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 4,
            enclosure: 'Paddock A',
        );

        self::assertSame('Small', $dino->getSpecification(), 'This is supposed to be a small Dinosaur');
    }
}
