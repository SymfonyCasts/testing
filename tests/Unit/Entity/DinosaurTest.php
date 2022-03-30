<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    public function testDinoOver15MetersIsLarge(int $length, string $expectedSize): void
    {
        $dino = new Dinosaur($length);

        self::assertSame(expected: $expectedSize, actual: $dino->getSpecification(), message: 'This is supposed to be a large dino!');
    }

    public function getDinoLengthForSpecification(): \Generator
    {
        yield [15, 'Large'];
        yield [10, 'Small'];
    }
}
