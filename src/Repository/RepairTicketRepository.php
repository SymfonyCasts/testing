<?php

namespace App\Repository;

use App\Entity\RepairTicket;
use App\Enum\TicketStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RepairTicket>
 */
class RepairTicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RepairTicket::class);
    }

    /**
     * @return list<RepairTicket>
     */
    public function findOpen(): array
    {
        return $this->findBy(['status' => TicketStatus::Open], ['createdAt' => 'ASC']);
    }
}
