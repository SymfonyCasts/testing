<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    public function testDinoOver15MetersIsLarge(): void
    {
        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 15,
            enclosure: 'Paddock A'
        );

        self::assertSame('Large', $dino->getSpecification(), 'This is supposed to be a large dino!');

        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 5,
            enclosure: 'Paddock A'
        );

        self::assertSame('Small', $dino->getSpecification(), 'This is supposed to be a small dino!');
    }
}
