<?php

declare(strict_types=1);

namespace App\Tests\Conversation\Controller;

use App\Conversation\Controller\GetPaginatedConversationMessagesController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\ConversationFactory;
use App\Tests\Factory\MessageFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(GetPaginatedConversationMessagesController::class)]
final class GetPaginatedConversationMessagesControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return GetPaginatedConversationMessagesController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/conversations/{id}/messages';
    }

    public function testOk(): void
    {
        // Arrange
        $initiator = UserFactory::new()->create([
            'email' => 'creator@mail.com',
        ]);
        $receiver = UserFactory::new()->create([
            'email' => 'receiver@mail.com',
        ]);
        $conversation = ConversationFactory::new()->create([
            'name' => 'Conversation 1',
            'initializedBy' => $initiator,
            'receiver' => $receiver,
        ]);

        MessageFactory::new()->sequence([
            [
                'content' => 'Bonjour, comment ça va ?',
                'sentBy' => $initiator,
                'sentTo' => $receiver,
            ],
            [
                'content' => 'Ça va bien, merci.',
                'sentBy' => $receiver,
                'sentTo' => $initiator,
            ],
            [
                'content' => 'Et toi ?',
                'sentBy' => $initiator,
                'sentTo' => $receiver,
            ],
        ])->create([
            'conversation' => $conversation,
        ]);

        // Act
        $this->request(Request::METHOD_GET, parameters: [
            'id' => $conversation->getId(),
        ]);

        // Assert
        self::assertResponseStatusCodeSame(200);
        self::assertJsonResponseFile();
    }

    public function testWithEmptyMessages(): void
    {
        // Arrange
        $initiator = UserFactory::new()->create([
            'email' => 'creator@mail.com',
        ]);
        $receiver = UserFactory::new()->create([
            'email' => 'receiver@mail.com',
        ]);
        $conversation = ConversationFactory::new()->create([
            'name' => 'Conversation 1',
            'initializedBy' => $initiator,
            'receiver' => $receiver,
        ]);

        // Act
        $this->request(Request::METHOD_GET, parameters: [
            'id' => $conversation->getId(),
        ]);

        // Assert
        self::assertResponseStatusCodeSame(200);
        self::assertJsonResponseFile();
    }
}
