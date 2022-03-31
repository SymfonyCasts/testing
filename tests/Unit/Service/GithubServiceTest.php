<?php

namespace App\Tests\Unit\Service;

use App\Service\GithubService;
use PHPUnit\Framework\TestCase;

class GithubServiceTest extends TestCase
{
    public function testGetDinoHealthReports(): void
    {
        $expected = [
            ['name' => 'Big Eaty', 'health' => 'Sick'],
            ['name' => 'Maverick', 'health' => 'Healthy'],
        ];

        $service = new GithubService();

        $results = $service->getHealthReports();

        self::assertEquals($expected, $results);
    }
}
