<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Conversation\Entity\Conversation;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Conversation>
 */
final class ConversationFactory extends PersistentProxyObjectFactory
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
