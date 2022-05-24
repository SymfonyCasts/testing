<?php

namespace App\Tests\Unit\Service;

use App\Entity\Dinosaur;
use PHPUnit\Framework\TestCase;

class GithubServiceTest extends TestCase
{
    public function testGetDinoHealthReports(): void
    {
        $service = new GithubService();

        $expectedDinos = [
            (new Dinosaur('Daisy', 'Velociraptor', 2, 'Paddock A'))->setHealth('SICK'),
            new Dinosaur('Maverick','Pterodactyl', 7, 'Aviary 1')
        ];

        self::assertSame($expectedDinos,
            $service->getHealthReports([
                new Dinosaur('Daisy', 'Velociraptor', 2, 'Paddock A'),
                new Dinosaur('Maverick','Pterodactyl', 7, 'Aviary 1')
            ])
        );
    }
}
