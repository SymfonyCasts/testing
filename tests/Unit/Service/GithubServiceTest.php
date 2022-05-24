<?php

namespace App\Tests\Unit\Service;

use App\Entity\Dinosaur;
use App\Service\GithubService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GithubServiceTest extends TestCase
{
    public function testGetDinoHealthReports(): void
    {
        $mockLogger = $this->createMock(LoggerInterface::class);

        $service = new GithubService($mockLogger);

        $expectedDinos = [
            (new Dinosaur('Daisy', 'Velociraptor', 2, 'Paddock A'))->setHealth('SICK'),
            new Dinosaur('Maverick','Pterodactyl', 7, 'Aviary 1')
        ];

        self::assertEquals($expectedDinos,
            $service->getHealthReports([
                new Dinosaur('Daisy', 'Velociraptor', 2, 'Paddock A'),
                new Dinosaur('Maverick','Pterodactyl', 7, 'Aviary 1')
            ])
        );
    }
}
