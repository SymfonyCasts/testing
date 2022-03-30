<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class TruthTest extends TestCase
{
    public function testIsItTrue(): void
    {
        self::assertTrue(condition: true, message: 'We know this will fail because false is never true!');
    }
}
