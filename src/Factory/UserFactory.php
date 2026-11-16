<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * Pass a plain-text `password` attribute: it's hashed before the user is created.
 *
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public const string DEFAULT_PASSWORD = 'engage';

    public function __construct(private PasswordHasherFactoryInterface $hasherFactory)
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    public function engineer(): self
    {
        return $this->with(['roles' => ['ROLE_ENGINEER']]);
    }

    protected function defaults(): array
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'name' => self::faker()->firstName().' '.self::faker()->lastName(),
            'password' => self::DEFAULT_PASSWORD,
        ];
    }

    protected function initialize(): static
    {
        return $this->beforeInstantiate(function (array $attributes): array {
            $attributes['password'] = $this->hasherFactory
                ->getPasswordHasher(User::class)
                ->hash($attributes['password']);

            return $attributes;
        });
    }
}
