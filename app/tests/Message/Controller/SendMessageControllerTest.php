<?php

declare(strict_types=1);

namespace App\Tests\Message\Controller;

use App\Conversation\Controller\SendMessageController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;

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
        return '/api/messages';
    }

    public function testOk(): void
    {
        // Arrange
        AnnounceFactory::new([
            'createdBy' => 'admin',
        ])->create();

        // Act
        $this->request('POST', json: [
            'content' => 'Hello',
            'announceId' => 1,
        ]);

        // Assert
        self::assertResponseStatusCodeSame(201);
        $this->assertJsonResponseFile();
    }
}
