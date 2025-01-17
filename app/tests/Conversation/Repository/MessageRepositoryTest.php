<?php

declare(strict_types=1);

namespace App\Tests\Conversation\Repository;

use App\Conversation\Repository\MessageRepository;
use App\Shared\Api\PaginationFilterQuery;
use App\Tests\Factory\ConversationFactory;
use App\Tests\Factory\MessageFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @internal
 */
#[CoversClass(MessageRepository::class)]
final class MessageRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testGetMessagesForConversation(): void
    {
        // Arrange
        $conversation = ConversationFactory::new()->create();
        MessageFactory::new()->many(5)->create([
            'conversation' => $conversation,
        ]);

        // Act
        $results = MessageFactory::repository()->getMessagesForConversation(
            $conversation->getId(),
            new PaginationFilterQuery()
        );

        // Assert
        MessageFactory::repository()->assert()->count(5);
        self::assertCount(5, $results->getIterator());
    }

    public function testGetMessagesForConversationWithNoConversation(): void
    {
        // Act
        $results = MessageFactory::repository()->getMessagesForConversation(1, new PaginationFilterQuery());

        // Assert
        MessageFactory::repository()->assert()->empty();
        self::assertCount(0, $results->getIterator());
    }
}
