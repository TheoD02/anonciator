<?php

namespace App\Tests\Message\Controller;

use App\Message\Controller\SendMessageController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;

class SendMessageControllerTest extends AbstractApiWebTestCase
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
