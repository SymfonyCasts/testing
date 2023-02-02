<?php

namespace App\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    public function testItWorks(): void
    {
        self::assertEquals(42, 42);
    }

    public function itWorksTheSame(): void
    {
        self::assertSame(42, 42);
    }
}
