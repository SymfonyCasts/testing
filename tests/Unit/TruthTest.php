<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class TruthTest extends TestCase
{
    public function testIsItTrue(): void
    {
        self::assertTrue(condition: false, message: 'We know this will fail!');
    }
}
