<?php

namespace App\Controller;

use App\Service\DinoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route(path: '/', name: 'main_controller', methods: ['GET'])]
    public function index(DinoService $dinoService): Response
    {
        return $this->render('base.html.twig', [
            'dinos' => $dinoService->getDinosaurs(),
        ]);
    }
}
