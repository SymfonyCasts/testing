<?php

namespace App\Tests\Unit\Service;

use App\Service\GithubService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GithubServiceTest extends TestCase
{
    public function testGetDinoHealthReports(): void
    {
        $expected = [
            ['name' => 'Big Eaty', 'health' => 'Sick'],
            ['name' => 'Maverick', 'health' => 'Healthy'],
        ];

        $data = [
            [
                'title' => 'Big Eaty',
                'labels' => [['name' => 'Sick']]
            ],
            [
                'title' => 'Maverick',
                'labels' => [['name' => 'Healthy']]
            ],
        ];


        $mockedResponse = $this->createMock(ResponseInterface::class);
        $mockedResponse
            ->expects(self::once())
            ->method('toArray')
            ->willReturn($data)
        ;

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with('GET', 'https://api.github.com/repos/jrushlow/nothing-here/issues')
            ->willReturn($mockedResponse)
        ;

        $service = new GithubService($httpClient);

        $results = $service->getHealthReports();

        self::assertEquals($expected, $results);
    }
}
