<?php

namespace App\Entity;

use App\Enum\TicketPriority;
use App\Enum\TicketStatus;
use App\Repository\RepairTicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RepairTicketRepository::class)]
class RepairTicket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['ticket:read'])]
    private TicketStatus $status = TicketStatus::Open;

    public function __construct(
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        #[Groups(['ticket:read'])]
        private Ship $ship,

        #[ORM\Column(length: 255)]
        #[Groups(['ticket:read'])]
        private string $reporterEmail,

        #[ORM\Column(type: 'text')]
        #[Groups(['ticket:read'])]
        private string $description,

        #[ORM\Column]
        #[Groups(['ticket:read'])]
        private TicketPriority $priority = TicketPriority::Normal,

        #[ORM\Column]
        #[Groups(['ticket:read'])]
        private \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['ticket:read'])]
    public function getNumber(): string
    {
        return \sprintf('SD-%03d', $this->id);
    }

    public function getShip(): Ship
    {
        return $this->ship;
    }

    public function getReporterEmail(): string
    {
        return $this->reporterEmail;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPriority(): TicketPriority
    {
        return $this->priority;
    }

    public function isCritical(): bool
    {
        return $this->priority === TicketPriority::Critical;
    }

    public function getStatus(): TicketStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function resolve(): void
    {
        $this->status = TicketStatus::Resolved;
    }
}
