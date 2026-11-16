<?php

namespace App\Controller;

use App\Entity\Ship;
use App\Repository\ShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShipController extends AbstractController
{
    #[Route('/ships', name: 'app_ships')]
    public function index(ShipRepository $ships): Response
    {
        return $this->render('ship/index.html.twig', [
            'ships' => $ships->findInDock(),
        ]);
    }

    #[Route('/ships/{slug:ship}', name: 'app_ship_show')]
    public function show(Ship $ship): Response
    {
        return $this->render('ship/show.html.twig', [
            'ship' => $ship,
        ]);
    }
}
