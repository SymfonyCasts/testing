<?php

namespace App\Tests\Unit\Service;

use App\Entity\Dinosaur;
use App\Service\DinoService;
use PHPUnit\Framework\TestCase;

class DinoServiceTest extends TestCase
{
    public function testDinoHealth(): void
    {
        $service = new DinoService();

        $expectedDinos = [
            new Dinosaur('Daisy', 'Velociraptor', 2, 'Paddock A'),
            new Dinosaur('Maverick', 'Pterodactyl', 7, 'Aviary 1'),
        ];

        $sickDino = new Dinosaur('Big Eaty', 'Tyrannosaurus', 15, 'Paddock B');
        $sickDino->healthy = false;

        $expectedDinos[] = $sickDino;

        self::assertEquals($expectedDinos, $service->getDinosaurs());
    }
}
