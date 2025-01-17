<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Announce\Entity\Announce;
use App\Announce\Enum\AnnounceStatus;
use App\User\Entity\User;
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

    public function withCreator(User $user): self
    {
        return $this->with(['createdBy' => $user->getUserIdentifier()]);
    }

    protected function defaults(): array
    {
        return [
            'category' => AnnounceCategoryFactory::randomOrCreate(),
            'description' => self::faker()->text(50),
            'location' => '10.00',
            'price' => (string)self::faker()->randomFloat(2, 0, 1000),
            'status' => self::faker()->randomElement(AnnounceStatus::cases()),
            'title' => self::faker()->text(30),
            'createdBy' => UserFactory::new()->createOne()->getUserIdentifier(),
        ];
    }
}
