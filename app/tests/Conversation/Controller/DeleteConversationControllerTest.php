<?php

declare(strict_types=1);

namespace App\Tests\Conversation\Controller;

use App\Conversation\Controller\DeleteConversationController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\ConversationFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(DeleteConversationController::class)]
final class DeleteConversationControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return DeleteConversationController::class;
    }

    public function testOk(): void
    {
        // Arrange
        $conversation = ConversationFactory::new()->create();

        // Act
        $this->request(Request::METHOD_DELETE, parameters: [
            'id' => $conversation->getId(),
        ]);

        // Assert
        self::assertResponseStatusCodeSame(204);
    }

    public function testNotFound(): void
    {
        // Act
        $this->request(Request::METHOD_DELETE, parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseStatusCodeSame(404);
        $this->assertJsonResponseFile();
    }

    public function expectedUrl(): string
    {
        return '/api/conversations/{id}';
    }
}
