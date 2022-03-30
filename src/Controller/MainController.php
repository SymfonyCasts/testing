<?php

namespace App\Controller;

use App\Service\GitHubDinoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route(path: '/', name: 'main_controller', methods: ['GET'])]
    public function index(GitHubDinoService $dinoService): Response
    {
        $issues = $dinoService->getDinoIssues();

        return $this->render('base.html.twig', [
            'dinos' => $issues,
        ]);
    }
}
