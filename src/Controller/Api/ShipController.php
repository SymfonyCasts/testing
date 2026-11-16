<?php

namespace App\Controller\Api;

use App\Entity\Ship;
use App\Repository\ShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/ships', format: 'json')]
final class ShipController extends AbstractController
{
    #[Route('', name: 'api_ships', methods: ['GET'])]
    public function index(ShipRepository $ships): JsonResponse
    {
        return $this->json($ships->findInDock(), context: ['groups' => 'ship:read']);
    }

    #[Route('/{slug:ship}', name: 'api_ship_show', methods: ['GET'])]
    public function show(Ship $ship): JsonResponse
    {
        return $this->json($ship, context: ['groups' => 'ship:read']);
    }
}
