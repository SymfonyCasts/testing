<?php

namespace App\Controller;

use App\Entity\Dinosaur;
use App\Repository\DinosaurRepository;
use App\Service\GithubService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route(path: '/', name: 'main_controller', methods: ['GET'])]
    public function index(GithubService $github, DinosaurRepository $dinosaurRepository): Response
    {
        $dinos = $dinosaurRepository->findAll();

        foreach ($dinos as $dino) {
            $dino->setHealth($github->getHealthReport($dino->getName()));
        }

        return $this->render('main/index.html.twig', [
            'dinos' => $dinos,
        ]);
    }
}
