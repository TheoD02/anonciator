<?php

declare(strict_types=1);

namespace App\Tests\Conversation\Repository;

use App\Conversation\Repository\ConversationRepository;
use App\Tests\Factory\AnnounceFactory;
use App\Tests\Factory\ConversationFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @internal
 */
#[CoversClass(ConversationRepository::class)]
final class ConversationRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testGetConversationMatchingAnnounceAndUserWhenAnythingExists(): void
    {
        // Act
        $result = ConversationFactory::repository()->getConversationMatchingAnnounceAndUser(
            announceId: 1,
            userInitiatorId: 1,
            userReceiverId: 2
        );

        // Assert
        self::assertNull($result);
    }

    public function testGetConversationMatchingAnnounceAndUserWhenConversationExists(): void
    {
        // Arrange
        $announceCreator = UserFactory::new()->create();
        $loggedUser = UserFactory::new()->create();
        $announce = AnnounceFactory::new()->create();
        ConversationFactory::new()->create([
            'announce' => $announce,
            'initializedBy' => $loggedUser,
            'receiver' => $announceCreator,
        ]);

        // Act
        $result = ConversationFactory::repository()->getConversationMatchingAnnounceAndUser(
            announceId: $announce->getId(),
            userInitiatorId: $loggedUser->getId(),
            userReceiverId: $announceCreator->getId()
        );

        // Assert
        self::assertNotNull($result);
        self::assertSame($announce->getId(), $result->getAnnounce()->getId());
        self::assertSame($loggedUser->getId(), $result->getInitializedBy()->getId());
        self::assertSame($announceCreator->getId(), $result->getReceiver()->getId());
    }
}
