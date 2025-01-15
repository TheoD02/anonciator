<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Announce\Entity\AnnounceCategory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<AnnounceCategory>
 */
final class AnnounceCategoryFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return AnnounceCategory::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->randomElement(
                ['Accessories', 'Cars', 'Clothes', 'Electronics', 'Furniture', 'Real Estate']
            ),
        ];
    }
}
