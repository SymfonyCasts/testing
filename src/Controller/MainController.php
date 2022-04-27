<?php

namespace App\Controller;

use App\Entity\Dinosaur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route(path: '/', name: 'main_controller', methods: ['GET'])]
    public function index(): Response
    {
        $healthReports = [
            ['name' => 'Big Eaty', 'health' => 'Sick'],
            // more healthReports from github go in this array
        ];

        $dinos = [
            new Dinosaur('Daisy', 'Velociraptor', 2, 'Paddock A'),
            new Dinosaur('Maverick','Pterodactyl', 7, 'Aviary 1'),
            new Dinosaur('Big Eaty', 'Tyrannosaurus', 15, 'Paddock C'),
            new Dinosaur('Dennis', 'Dilophosaurus', 10, 'Paddock B'),
            new Dinosaur('Bumpy', 'Triceratops', 10, 'Paddock B'),
        ];

        array_walk($dinos, function (Dinosaur $dino) use ($healthReports){
            foreach ($healthReports as $report) {
                if ($report['name'] === $dino->getName()) {
                    dump($dino);
                    $dino->setHealth($report['health']);
                }
            }
        });

        return $this->render('base.html.twig', [
            'dinos' => $dinos,
        ]);
    }
}
