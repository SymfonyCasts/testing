<?php

namespace App\Controller\Api;

use App\Dto\RepairRequest;
use App\Entity\Ship;
use App\Repository\RepairTicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api', format: 'json')]
final class TicketController extends AbstractController
{
    #[Route('/tickets', name: 'api_tickets', methods: ['GET'])]
    #[IsGranted('ROLE_ENGINEER')]
    public function index(RepairTicketRepository $tickets): JsonResponse
    {
        return $this->json($tickets->findOpen(), context: ['groups' => 'ticket:read']);
    }

    #[Route('/ships/{slug:ship}/tickets', name: 'api_ship_tickets_create', methods: ['POST'])]
    public function create(
        Ship $ship,

        #[MapRequestPayload]
        RepairRequest $repairRequest,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $ticket = $repairRequest->createTicketFor($ship);
        $entityManager->persist($ticket);
        $entityManager->flush();

        return $this->json($ticket, JsonResponse::HTTP_CREATED, context: ['groups' => 'ticket:read']);
    }
}
