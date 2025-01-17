<?php

declare(strict_types=1);

namespace App\Tests\Conversation\Controller;

use App\Conversation\Controller\InitiateConversationController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(InitiateConversationController::class)]
class InitiateConversationControllerTest extends AbstractApiWebTestCase
{
    public function testOk(): void
    {
        // Arrange
        $loggedUser = UserFactory::admin();
        $announceCreator = UserFactory::new()->create([
            'email' => 'creator@mail.com',
        ]);
        $announce = AnnounceFactory::new()->create([
            'createdBy' => $announceCreator->getEmail(),
        ]);

        // Act
        $this->request(method: Request::METHOD_GET, parameters: [
            'announceId' => $announce->getId(),
        ],);

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }

    public function testShouldFailOnSelfCreatedAnnounce(): void
    {
        // Arrange
        $loggedUser = UserFactory::admin();
        $announce = AnnounceFactory::new()->create([
            'createdBy' => $loggedUser->getEmail(),
        ]);

        // Act
        $this->request(method: Request::METHOD_GET, parameters: [
            'announceId' => $announce->getId(),
        ],);

        // Assert
        self::assertResponseStatusCodeSame(422);
        $this->assertJsonResponseFile();
    }

    public function getAction(): string
    {
        return InitiateConversationController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/conversations/initiate/{announceId}';
    }
}
