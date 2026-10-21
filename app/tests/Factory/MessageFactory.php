<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Conversation\Entity\Message;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Message>
 */
final class MessageFactory extends PersistentObjectFactory
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
