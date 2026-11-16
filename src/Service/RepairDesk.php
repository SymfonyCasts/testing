<?php

namespace App\Service;

use App\Entity\RepairTicket;
use App\Message\SoundRedAlert;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Files repair tickets booked through the website or the API.
 */
final readonly class RepairDesk
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private MessageBusInterface $bus,
    ) {
    }

    public function file(RepairTicket $ticket): void
    {
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        $this->mailer->send($this->confirmationFor($ticket));

        if ($ticket->isCritical()) {
            $this->bus->dispatch(new SoundRedAlert($ticket->getId()));
        }
    }

    private function confirmationFor(RepairTicket $ticket): TemplatedEmail
    {
        $ship = $ticket->getShip();

        return new TemplatedEmail()
            ->to($ticket->getReporterEmail())
            ->subject(\sprintf('Repair ticket %s filed for %s', $ticket->getNumber(), $ship->getName()))
            ->htmlTemplate('email/repair_confirmation.html.twig')
            ->context(['ticket' => $ticket])
        ;
    }
}
