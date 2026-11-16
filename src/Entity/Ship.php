<?php

namespace App\Entity;

use App\Enum\ShipStatus;
use App\Enum\SystemStatus;
use App\Enum\SystemType;
use App\Repository\ShipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: ShipRepository::class)]
class Ship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ship:read', 'ticket:read'])]
    private string $name;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['ship:read', 'ticket:read'])]
    private string $slug;

    #[ORM\Column(length: 255)]
    #[Groups(['ship:read'])]
    private string $model;

    #[ORM\Column(length: 255)]
    #[Groups(['ship:read'])]
    private string $captain;

    #[ORM\Column]
    #[Groups(['ship:read'])]
    private \DateTimeImmutable $arrivedAt;

    #[ORM\Column]
    #[Groups(['ship:read'])]
    private ShipStatus $status = ShipStatus::InDock;

    /**
     * @var Collection<int, ShipSystem>
     */
    #[ORM\OneToMany(targetEntity: ShipSystem::class, mappedBy: 'ship', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => \SortDirection::Ascending])]
    #[Groups(['ship:read'])]
    private Collection $systems;

    public function __construct(
        string $name,
        string $model,
        string $captain,
        \DateTimeImmutable $arrivedAt = new \DateTimeImmutable(),
    ) {
        $this->name = $name;
        $this->model = $model;
        $this->captain = $captain;
        $this->arrivedAt = $arrivedAt;
        $this->slug = (new AsciiSlugger())->slug($name)->lower()->toString();
        $this->systems = new ArrayCollection(array_map(
            fn (SystemType $type) => new ShipSystem($this, $type),
            SystemType::cases(),
        ));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getCaptain(): string
    {
        return $this->captain;
    }

    public function getArrivedAt(): \DateTimeImmutable
    {
        return $this->arrivedAt;
    }

    public function getStatus(): ShipStatus
    {
        return $this->status;
    }

    public function isInDock(): bool
    {
        return ShipStatus::InDock === $this->status;
    }

    public function depart(): void
    {
        $this->status = ShipStatus::Departed;
    }

    /**
     * @return Collection<int, ShipSystem>
     */
    public function getSystems(): Collection
    {
        return $this->systems;
    }

    public function reportFault(SystemType $type): void
    {
        $this->systems
            ->findFirst(fn (int $key, ShipSystem $system) => $system->is($type))
            ?->fault();
    }

    public function countFaults(): int
    {
        return $this->systems
            ->filter(fn (ShipSystem $system) => SystemStatus::Fault === $system->getStatus())
            ->count();
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
