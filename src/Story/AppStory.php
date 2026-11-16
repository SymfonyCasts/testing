<?php

namespace App\Story;

use App\Enum\SystemType;
use App\Enum\TicketPriority;
use App\Factory\RepairTicketFactory;
use App\Factory\ShipFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\lazy;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        $this->createUsers();
        $this->createShips();

        RepairTicketFactory::new()
            ->with(['ship' => lazy(static fn () => ShipFactory::random())])
            ->many(8)
            ->create();
        RepairTicketFactory::new()
            ->resolved()
            ->with(['ship' => lazy(static fn () => ShipFactory::random())])
            ->many(5)
            ->create();
    }

    private function createUsers(): void
    {
        UserFactory::new()->engineer()->create([
            'email' => 'scotty@shipshape.space',
            'name' => 'Montgomery Scotch',
            'apiToken' => 'scotty-api-token',
        ]);
        UserFactory::new()->engineer()->create([
            'email' => 'belanna@shipshape.space',
            'name' => 'B\'Elanna Torque',
            'apiToken' => 'belanna-api-token',
        ]);
        UserFactory::createOne([
            'email' => 'wesley@shipshape.space',
            'name' => 'Cadet Wesley Crushed',
            'apiToken' => 'wesley-api-token',
        ]);
    }

    private function createShips(): void
    {
        $leafyCruiser = ShipFactory::new()->withFaults(SystemType::Shields)->create([
            'name' => 'USS LeafyCruiser',
            'model' => 'Garden-class',
            'captain' => 'Capt. Jean-Luc Pickles',
            'arrivedAt' => new \DateTimeImmutable('-2 days'),
        ]);
        ShipFactory::new()->withFaults(SystemType::WarpCore, SystemType::Navigation)->create([
            'name' => 'USS Espresso',
            'model' => 'Latte-class',
            'captain' => 'Capt. James T. Quick!',
            'arrivedAt' => new \DateTimeImmutable('-5 hours'),
        ]);
        ShipFactory::new()->departed()->create([
            'name' => 'USS Wanderlust',
            'model' => 'Delta Tourist',
            'captain' => 'Capt. Mira Wanderlust',
            'arrivedAt' => new \DateTimeImmutable('-3 weeks'),
        ]);
        ShipFactory::new()->withFaults(SystemType::LifeSupport, SystemType::Comms, SystemType::Shields)->create([
            'name' => 'USS Duct Tape',
            'model' => 'Patchwork-class',
            'captain' => 'Capt. Ricky Rivets',
            'arrivedAt' => new \DateTimeImmutable('-1 week'),
        ]);
        ShipFactory::createOne([
            'name' => 'USS Snoozebeam',
            'model' => 'Siesta-class',
            'captain' => 'Capt. Kathryn Jamway',
            'arrivedAt' => new \DateTimeImmutable('-1 day'),
        ]);

        ShipFactory::new()->withFaults(SystemType::Shields)->many(4)->create();
        ShipFactory::new()->departed()->many(3)->create();

        RepairTicketFactory::createOne([
            'ship' => $leafyCruiser,
            'reporterEmail' => 'pickles@leafycruiser.space',
            'description' => 'Shields flicker every time the hydroponics bay waters the ferns.',
            'priority' => TicketPriority::Critical,
        ]);
    }
}
