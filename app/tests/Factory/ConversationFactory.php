<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Conversation\Entity\Conversation;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Conversation>
 */
final class ConversationFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Conversation::class;
    }

    protected function defaults(): array
    {
        return [
            'initializedBy' => UserFactory::new(),
            'name' => self::faker()->text(200),
            'receiver' => UserFactory::new(),
        ];
    }
}
