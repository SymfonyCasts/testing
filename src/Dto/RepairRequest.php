<?php

namespace App\Dto;

use App\Entity\RepairTicket;
use App\Entity\Ship;
use App\Enum\TicketPriority;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * What someone fills in to book a repair: the web form and the API both validate this.
 */
final class RepairRequest
{
    #[Assert\NotBlank(message: 'Where should we send updates?')]
    #[Assert\Email]
    public ?string $reporterEmail = null;

    #[Assert\NotBlank(message: 'Tell our engineers what\'s wrong.')]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: 'Give our engineers a bit more to go on: at least {{ limit }} characters.',
    )]
    public ?string $description = null;

    #[Assert\NotNull]
    public ?TicketPriority $priority = TicketPriority::Normal;

    public function createTicketFor(Ship $ship): RepairTicket
    {
        return new RepairTicket(
            $ship,
            (string) $this->reporterEmail,
            (string) $this->description,
            $this->priority ?? TicketPriority::Normal,
        );
    }
}
