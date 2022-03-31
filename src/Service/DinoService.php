<?php

namespace App\Service;

use App\Entity\Dinosaur;

class DinoService
{
    public function getDinosaurs(): array
    {
        $dinos = [
            new Dinosaur('Daisy', 'Velociraptor', 2, 'Paddock A'),
            new Dinosaur('Maverick', 'Pterodactyl', 7, 'Aviary 1'),
            new Dinosaur('Big Eaty', 'Tyrannosaurus', 15, 'Paddock B'),
        ];

        return $dinos;
    }
}
