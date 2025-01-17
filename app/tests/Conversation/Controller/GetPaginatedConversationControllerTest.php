<?php

declare(strict_types=1);

namespace App\Tests\Conversation\Controller;

use App\Conversation\Controller\GetPaginatedConversationController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\ConversationFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(GetPaginatedConversationController::class)]
class GetPaginatedConversationControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return GetPaginatedConversationController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/conversations';
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
        ConversationFactory::new()
            ->sequence([
                [
                    'name' => 'Conversation 1',
                ],
                [
                    'name' => 'Conversation 2',
                ],
                [
                    'name' => 'Conversation 3',
                ],
            ])
            ->create([
                'initializedBy' => $initiator,
                'receiver' => $receiver,
            ])
        ;

        // Act
        $this->request(Request::METHOD_GET);

        // Assert
        self::assertResponseStatusCodeSame(200);
        self::assertJsonResponseFile();
    }

    public function testEmpty(): void
    {
        // Act
        $this->request(Request::METHOD_GET);

        // Assert
        self::assertResponseStatusCodeSame(200);
        self::assertJsonResponseFile();
    }
}
