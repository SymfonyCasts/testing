<?php

namespace App\Entity;

use App\Enum\SystemStatus;
use App\Enum\SystemType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
class ShipSystem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'systems')]
    #[ORM\JoinColumn(nullable: false)]
    private Ship $ship;

    #[ORM\Column]
    #[Groups(['ship:read'])]
    private SystemType $type;

    #[ORM\Column]
    #[Groups(['ship:read'])]
    private SystemStatus $status = SystemStatus::Ok;

    public function __construct(Ship $ship, SystemType $type)
    {
        $this->ship = $ship;
        $this->type = $type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShip(): Ship
    {
        return $this->ship;
    }

    public function getType(): SystemType
    {
        return $this->type;
    }

    public function getStatus(): SystemStatus
    {
        return $this->status;
    }

    public function is(SystemType $type): bool
    {
        return $this->type === $type;
    }

    public function fault(): void
    {
        $this->status = SystemStatus::Fault;
    }
}
