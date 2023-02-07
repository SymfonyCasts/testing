<?php

namespace App\DataFixtures;

use App\Entity\Dinosaur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dinos = [
            new Dinosaur('Daisy', 'Velociraptor', 2, 'Paddock A'),
            new Dinosaur('Maverick','Pterodactyl', 7, 'Aviary 1'),
            new Dinosaur('Big Eaty', 'Tyrannosaurus', 15, 'Paddock C'),
            new Dinosaur('Dennis', 'Dilophosaurus', 6, 'Paddock B'),
            new Dinosaur('Bumpy', 'Triceratops', 10, 'Paddock B'),
        ];

        foreach ($dinos as $dino) {
            $manager->persist($dino);
        }

        $manager->flush();
    }
}
