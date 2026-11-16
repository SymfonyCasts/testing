<?php

namespace App\Controller;

use App\Dto\RepairRequest;
use App\Form\BookRepairType;
use App\Repository\ShipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookRepairController extends AbstractController
{
    #[Route('/book-repair', name: 'app_book_repair')]
    public function __invoke(Request $request, ShipRepository $ships, EntityManagerInterface $entityManager): Response
    {
        $repairRequest = new RepairRequest();
        $form = $this->createForm(BookRepairType::class, $repairRequest, [
            'ship' => $ships->findOneBySlug($request->query->getString('ship')),
        ]);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('book_repair/index.html.twig', [
                'form' => $form,
            ]);
        }

        $ship = $form->get('ship')->getData();
        $ticket = $repairRequest->createTicketFor($ship);
        $entityManager->persist($ticket);
        $entityManager->flush();

        $this->addFlash('success', \sprintf(
            'Repair ticket %s filed for %s. Our engineers are on it!',
            $ticket->getNumber(),
            $ship->getName(),
        ));

        return $this->redirectToRoute('app_ship_show', ['slug' => $ship->getSlug()]);
    }
}
