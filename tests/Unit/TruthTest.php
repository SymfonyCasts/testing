<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class TruthTest extends TestCase
{
    public function testIsItTrue(): void
    {
        self::assertSame(expected: true, actual: false);
    }
}
