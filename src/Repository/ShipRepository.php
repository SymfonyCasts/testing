<?php

namespace App\Repository;

use App\Entity\Ship;
use App\Enum\ShipStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ship>
 */
class ShipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ship::class);
    }

    /**
     * @return list<Ship>
     */
    public function findInDock(): array
    {
        return $this->findBy(['status' => ShipStatus::InDock], ['arrivedAt' => 'DESC']);
    }

    public function findOneBySlug(string $slug): ?Ship
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}
