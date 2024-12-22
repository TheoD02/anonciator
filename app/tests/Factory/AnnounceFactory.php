<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Announce\AnnounceStatus;
use App\Announce\Entity\Announce;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Announce>
 */
final class AnnounceFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Announce::class;
    }

    protected function defaults(): array
    {
        return [
            'category' => AnnounceCategoryFactory::randomOrCreate(),
            'description' => self::faker()->text(50),
            'location' => '10.00',
            'price' => '100.00',
            'status' => self::faker()->randomElement(AnnounceStatus::cases()),
            'title' => self::faker()->text(30),
        ];
    }
}
