<?php

namespace App\Factory;

use App\Entity\RepairTicket;
use App\Enum\TicketPriority;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<RepairTicket>
 */
final class RepairTicketFactory extends PersistentObjectFactory
{
    private const array DESCRIPTIONS = [
        'Warp core hums in B-flat. It used to hum in C.',
        'Shields drop whenever someone microwaves fish in the galley.',
        'Life support smells faintly of burnt toast. Nobody is making toast.',
        'Navigation insists every destination is "left".',
        'Comms only pick up a 24-hour cooking channel.',
        'Port thruster sneezes on startup.',
        'Replicator produces tea that is lukewarm, never hot.',
        'Artificial gravity takes Tuesdays off.',
        'Cloaking device works, but now we cannot find the ship.',
        'Turbolift plays smooth jazz and refuses to stop.',
        'Sensors report a nebula in the captain\'s ready room.',
        'Main viewscreen is stuck on a screensaver of flying toasters.',
    ];

    public static function class(): string
    {
        return RepairTicket::class;
    }

    public function resolved(): self
    {
        return $this->afterInstantiate(static fn (RepairTicket $ticket) => $ticket->resolve());
    }

    protected function defaults(): array
    {
        return [
            'ship' => ShipFactory::new(),
            'reporterEmail' => self::faker()->safeEmail(),
            'description' => self::faker()->randomElement(self::DESCRIPTIONS),
            'priority' => self::faker()->randomElement(TicketPriority::cases()),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-2 weeks')),
        ];
    }
}
