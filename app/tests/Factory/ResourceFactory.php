<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Resource\Entity\Resource;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Resource>
 */
final class ResourceFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Resource::class;
    }

    protected function defaults(): array
    {
        return [
            'bucket' => self::faker()->text(10),
            'path' => self::faker()->text(10),
        ];
    }
}
