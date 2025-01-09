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

    protected function defaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'username' => self::faker()->userName(),
        ];
    }
}
