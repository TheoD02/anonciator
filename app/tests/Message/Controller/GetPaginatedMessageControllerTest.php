<?php

declare(strict_types=1);

namespace App\Tests\Message\Controller;

use App\Conversation\Controller\GetPaginatedMessageController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;
use App\Tests\Factory\MessageFactory;

/**
 * @internal
 */
final class GetPaginatedMessageControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return GetPaginatedMessageController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/messages/{announceId}';
    }

    public function testOk(): void
    {
        // Arrange
        [$announce1, $announce2] = AnnounceFactory::new()->many(2)->create();
        MessageFactory::new([
            'announce' => $announce1,
        ])
            ->sequence([
                [
                    'content' => 'Hello, is available ?',
                ],
                [
                    'content' => 'Yes, it is available',
                ],
                [
                    'content' => 'Great, I will take it',
                ],
            ])
            ->create()
        ;

        // Act
        $this->request('GET', parameters: [
            'announceId' => $announce1->getId(),
        ]);

        // Assert
        self::assertResponseStatusCodeSame(200);
        $this->assertJsonResponseFile();
    }

    public function testAnnounceWithNoMessage(): void
    {
        // Arrange
        [$announce1, $announce2] = AnnounceFactory::new()->many(2)->create();
        MessageFactory::new([
            'announce' => $announce1,
        ])
            ->sequence([
                [
                    'content' => 'Hello, is available ?',
                ],
                [
                    'content' => 'Yes, it is available',
                ],
                [
                    'content' => 'Great, I will take it',
                ],
            ])
            ->create()
        ;

        // Act
        $this->request('GET', parameters: [
            'announceId' => $announce2->getId(),
        ]);

        // Assert
        self::assertResponseStatusCodeSame(200);
        $this->assertJsonResponseFile();
    }
}
