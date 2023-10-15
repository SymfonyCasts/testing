<?php

namespace App\Controller;

use App\Entity\Dinosaur;
use App\Repository\DinosaurRepository;
use App\Service\GithubService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route(path: '/', name: 'app_homepage', methods: ['GET'])]
    public function index(GithubService $github, DinosaurRepository $repository): Response
    {
        $dinos = $repository->findAll();

        foreach ($dinos as $dino) {
            $dino->setHealth($github->getHealthReport($dino->getName()));
        }

        return $this->render('main/index.html.twig', [
            'dinos' => $dinos,
        ]);
    }

    #[Route('/lockdown/end', name: 'app_lockdown_end', methods: ['POST'])]
    public function endLockDown(Request $request)
    {
        if (!$this->isCsrfTokenValid('end-lockdown', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        dd('todo');
    }
}
