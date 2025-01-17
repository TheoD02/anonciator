<?php

declare(strict_types=1);

namespace App\Tests\Message\Controller;

use App\Conversation\Controller\SendMessageController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;
use App\Tests\Factory\ConversationFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class SendMessageControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return SendMessageController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/conversations/{id}/messages';
    }

    public function testOk(): void
    {
        // Arrange
        $sender = UserFactory::admin();
        $receiver = UserFactory::new()->create([
            'username' => 'receiver',
            'email' => 'receiver@domain.tld',
        ]);
        $announce = AnnounceFactory::new([
            'createdBy' => $sender->getUserIdentifier(),
        ])->create();
        $conversation = ConversationFactory::new()->create([
            'announce' => $announce,
            'initializedBy' => $sender,
            'receiver' => $receiver,
        ]);

        // Act
        $this->request(
            method: Request::METHOD_POST,
            parameters: [
                'id' => $conversation->getId(),
            ],
            json: [
                'content' => 'Hello',
            ]
        );

        // Assert
        self::assertResponseStatusCodeSame(201);
        $this->assertJsonResponseFile();
    }
}
