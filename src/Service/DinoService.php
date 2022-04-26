<?php

namespace App\Service;

use App\Entity\Dinosaur;

class DinoService
{
    /**
     * @return Dinosaur[]
     */
    public function getDinosaurs(): array
    {
        return [
            new Dinosaur(name: 'Daisy', genus: 'Velociraptor', length: 2, enclosure: 'Paddock A'),
            new Dinosaur(name: 'Maverick', genus:'Pterodactyl', length: 7, enclosure: 'Aviary 1'),
            new Dinosaur(name: 'Big Eaty', genus: 'Tyrannosaurus', length: 15, enclosure: 'Paddock C'),
            new Dinosaur(name: 'Dennis', genus: 'Dilophosaurus', length: 10, enclosure: 'Paddock B'),
            new Dinosaur(name: 'Bumpy', genus: 'Triceratops', length: 10, enclosure: 'Paddock B'),
        ];
    }
}
