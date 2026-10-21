<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\User\Entity\GroupRole;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<GroupRole>
 */
final class GroupRoleFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return GroupRole::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->text(10),
        ];
    }
}
