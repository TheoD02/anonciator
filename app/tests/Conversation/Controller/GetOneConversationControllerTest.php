<?php

declare(strict_types=1);

namespace App\Tests\Conversation\Controller;

use App\Conversation\Controller\GetOneConversationController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\ConversationFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(GetOneConversationController::class)]
final class GetOneConversationControllerTest extends AbstractApiWebTestCase
{
    public function testOk(): void
    {
        // Arrange
        ConversationFactory::new()->create([
            'name' => 'Conversation 1',
            'initializedBy' => UserFactory::new()->create([
                'email' => 'creator@mail.com',
            ]),
            'receiver' => UserFactory::new()->create([
                'email' => 'receiver@mail.com',
            ]),
        ]);

        // Act
        $this->request(method: Request::METHOD_GET, parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseStatusCodeSame(200);
        self::assertJsonResponseFile();
    }

    public function testNotFound(): void
    {
        // Act
        $this->request(method: 'GET', parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseStatusCodeSame(404);
        $this->assertJsonResponseFile();
    }

    public function getAction(): string
    {
        return GetOneConversationController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/conversations/{id}';
    }
}
