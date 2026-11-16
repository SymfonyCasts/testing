<?php

namespace App\Controller;

use App\Repository\RepairTicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TicketController extends AbstractController
{
    #[Route('/tickets', name: 'app_tickets')]
    #[IsGranted('ROLE_ENGINEER')]
    public function index(RepairTicketRepository $tickets): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets->findOpen(),
        ]);
    }
}
