<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Announce\Entity\AnnounceCategory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<AnnounceCategory>
 */
final class AnnounceCategoryFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return AnnounceCategory::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->unique()->randomElement(
                ['Accessories', 'Cars', 'Clothes', 'Electronics', 'Furniture', 'Real Estate']
            ),
        ];
    }
}
