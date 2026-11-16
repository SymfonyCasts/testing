<?php

namespace App\Factory;

use App\Entity\Ship;
use App\Enum\SystemType;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Ship>
 */
final class ShipFactory extends PersistentObjectFactory
{
    private const array NAME_ADJECTIVES = [
        'Cosmic', 'Wobbly', 'Turbo', 'Sleepy', 'Rusty', 'Lunar', 'Plucky', 'Quantum', 'Zesty', 'Fuzzy',
        'Galactic', 'Sneaky', 'Jolly', 'Mighty', 'Squeaky', 'Stellar', 'Crispy', 'Bouncy', 'Dizzy', 'Nimble',
        'Grumpy', 'Shiny', 'Wandering', 'Breezy', 'Cheeky', 'Dapper', 'Gusty', 'Humble', 'Snazzy', 'Zippy',
    ];

    private const array NAME_NOUNS = [
        'Waffle', 'Otter', 'Comet', 'Teapot', 'Pancake', 'Badger', 'Biscuit', 'Nebula', 'Pickle', 'Walrus',
        'Muffin', 'Pigeon', 'Noodle', 'Asteroid', 'Gherkin', 'Penguin', 'Crumpet', 'Photon', 'Sloth', 'Acorn',
        'Moose', 'Pretzel', 'Quasar', 'Hedgehog', 'Dumpling', 'Lobster', 'Nugget', 'Pulsar', 'Turnip', 'Yak',
    ];

    private const array MODELS = [
        'Galaxy-class',
        'Nebula-class',
        'Comet-class',
        'Hauler-class',
        'Courier-class',
        'Scout-class',
    ];

    public static function class(): string
    {
        return Ship::class;
    }

    public function departed(): self
    {
        return $this->afterInstantiate(static fn (Ship $ship) => $ship->depart());
    }

    public function withFaults(SystemType ...$types): self
    {
        return $this->afterInstantiate(static function (Ship $ship) use ($types): void {
            foreach ($types as $type) {
                $ship->reportFault($type);
            }
        });
    }

    /**
     * @return list<string>
     */
    private static function names(): array
    {
        $names = [];

        foreach (self::NAME_ADJECTIVES as $adjective) {
            foreach (self::NAME_NOUNS as $noun) {
                $names[] = "USS {$adjective} {$noun}";
            }
        }

        return $names;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->unique()->randomElement(self::names()),
            'model' => self::faker()->randomElement(self::MODELS),
            'captain' => 'Capt. '.self::faker()->firstName().' '.self::faker()->lastName(),
            'arrivedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 month')),
        ];
    }
}
