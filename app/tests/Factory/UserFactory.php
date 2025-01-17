<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\User\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return User::class;
    }

    public static function admin(): User
    {
        return self::findOrCreate([
            'username' => 'admin',
            'email' => 'admin@domain.tld',
            'password' => '$2y$13$eAqv1hbaVJiLUgaC6p23nuuebHz9IqbNwoLoxh8Pu1lQJZJmm46Oe',
        ])->_real();
    }

    protected function defaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'username' => self::faker()->userName(),
            'password' => self::faker()->password(),
        ];
    }
}
