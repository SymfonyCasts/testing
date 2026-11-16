<?php

namespace App\MessageHandler;

use App\Message\SoundRedAlert;
use App\Repository\RepairTicketRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SoundRedAlertHandler
{
    public const string ENGINEERING_EMAIL = 'engineering@shipshape.space';

    public function __construct(
        private RepairTicketRepository $tickets,
        private MailerInterface $mailer,
    ) {
    }

    public function __invoke(SoundRedAlert $alert): void
    {
        $ticket = $this->tickets->find($alert->ticketId);

        if ($ticket === null) {
            return;
        }

        $this->mailer->send(new TemplatedEmail()
            ->to(self::ENGINEERING_EMAIL)
            ->subject(\sprintf('RED ALERT: %s for %s', $ticket->getNumber(), $ticket->getShip()->getName()))
            ->htmlTemplate('email/red_alert.html.twig')
            ->context(['ticket' => $ticket])
        );
    }
}
