<?php

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    public function testDinoHasProperties(): void
    {
        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 15,
            enclosure: 'Paddock A'
        );

        self::assertSame('Big Eaty', $dino->name);
        self::assertSame('Tyrannosaurus', $dino->genus);
        self::assertSame(15, $dino->length);
        self::assertSame('Paddock A', $dino->enclosure);
    }
}
