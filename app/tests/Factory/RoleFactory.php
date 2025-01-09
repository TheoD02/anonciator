<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\User\Entity\Role;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Role>
 */
final class RoleFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Role::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->text(10),
        ];
    }
}
