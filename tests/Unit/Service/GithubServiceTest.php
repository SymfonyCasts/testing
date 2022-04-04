<?php

namespace App\Tests\Unit\Service;

use App\Service\GithubService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class GithubServiceTest extends TestCase
{
    public function testGetDinoHealthReports(): void
    {
        $expected = [
            ['name' => 'Big Eaty', 'health' => 'Sick'],
            ['name' => 'Maverick', 'health' => 'Healthy'],
        ];

        $httpClient = HttpClient::create();

        $service = new GithubService($httpClient);

        $results = $service->getHealthReports();

        self::assertEquals($expected, $results);
    }
}
