<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Conversation\Entity\Message;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Message>
 */
final class MessageFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Message::class;
    }

    protected function defaults(): array
    {
        return [
            'content' => self::faker()->text(),
            'createdAt' => self::faker()->dateTime(),
            'sentBy' => self::faker()->text(),
            'sentTo' => self::faker()->text(),
            'updatedAt' => self::faker()->dateTime(),
        ];
    }
}
