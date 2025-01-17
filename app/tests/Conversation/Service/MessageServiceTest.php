<?php

declare(strict_types=1);

namespace App\Tests\Conversation\Service;

use App\Announce\Service\AnnounceService;
use App\Conversation\Dto\Payload\SendMessagePayload;
use App\Conversation\Service\ConversationService;
use App\Conversation\Service\MessageService;
use App\Shared\Api\RelationResolver;
use App\Tests\Factory\AnnounceFactory;
use App\Tests\Factory\ConversationFactory;
use App\Tests\Factory\UserFactory;
use App\User\Service\UserService;
use AutoMapper\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zenstruck\Foundry\Test\Factories;

/**
 * @internal
 */
#[CoversClass(MessageService::class)]
class MessageServiceTest extends TestCase
{
    use Factories;

    public function testCreateEntityFromPayloadLoggedUserToAnnounceCreator(): void
    {
        // Arrange
        $announceCreator = UserFactory::new()->create([
            'email' => 'creator@mail.com',
        ]);
        $loggedUser = UserFactory::admin();
        $announce = AnnounceFactory::new()->create([
            'createdBy' => $announceCreator->getUserIdentifier(),
        ]);
        $conversation = ConversationFactory::new()
            ->create([
                'announce' => $announce,
                'initializedBy' => $loggedUser,
                'receiver' => $announceCreator,
            ])
            ->_set('id', 1)
        ;

        $this->conversationServiceMock
            ->expects($this->once())
            ->method('getEntityById')
            ->with($conversation->getId(), true)
            ->willReturn($conversation)
        ;

        $this->userServiceMock
            ->expects($this->once())
            ->method('getOneByEmail')
            ->with($announce->getCreatedBy())
            ->willReturn($announceCreator)
        ;

        $messagePayload = new SendMessagePayload();
        $messagePayload->content = 'Hello World';

        // Act
        $message = $this->messageService->createMessageFromPayload(
            conversationId: $conversation->getId(),
            payload: $messagePayload,
            loggedUser: $loggedUser,
        );

        // Assert
        $this->assertSame($messagePayload->content, $message->getContent());
        $this->assertSame($loggedUser->getUserIdentifier(), $message->getSentBy());
        $this->assertSame($announceCreator->getUserIdentifier(), $message->getSentTo());
    }

    public function testCreateEntityFromPayloadAnnounceCreatorToConversationInitiator(): void
    {
        // Arrange
        $announceCreator = UserFactory::new()->create([
            'email' => 'creator@mail.com',
        ]);
        $loggedUser = UserFactory::admin();
        $announce = AnnounceFactory::new()->create([
            'createdBy' => $announceCreator->getUserIdentifier(),
        ]);
        $conversation = ConversationFactory::new()
            ->create([
                'announce' => $announce,
                'initializedBy' => $loggedUser,
                'receiver' => $announceCreator,
            ])
            ->_set('id', 1)
        ;

        $this->conversationServiceMock
            ->expects($this->once())
            ->method('getEntityById')
            ->with($conversation->getId(), true)
            ->willReturn($conversation)
        ;

        $this->userServiceMock
            ->expects($this->once())
            ->method('getOneByEmail')
            ->with($announce->getCreatedBy())
            ->willReturn($announceCreator)
        ;

        $messagePayload = new SendMessagePayload();
        $messagePayload->content = 'Hello World';

        // Act
        $message = $this->messageService->createMessageFromPayload(
            conversationId: $conversation->getId(),
            payload: $messagePayload,
            loggedUser: $announceCreator->_real(),
        );

        // Assert
        $this->assertSame($messagePayload->content, $message->getContent());
        $this->assertSame($loggedUser->getUserIdentifier(), $message->getSentTo());
        $this->assertSame($announceCreator->getUserIdentifier(), $message->getSentBy());
    }

    protected function setUp(): void
    {
        $this->conversationServiceMock = $this->createMock(ConversationService::class);
        $this->announceServiceMock = $this->createMock(AnnounceService::class);
        $this->userServiceMock = $this->createMock(UserService::class);

        $this->messageService = new MessageService(
            conversationService: $this->conversationServiceMock,
            announceService: $this->announceServiceMock,
            userService: $this->userServiceMock,
        );

        $this->messageService
            ->setEntityCrudServiceDependencies(
                mapper: $this->createMock(AutoMapperInterface::class),
                em: $this->createMock(EntityManagerInterface::class),
                dispatcher: $this->createMock(EventDispatcherInterface::class),
                relationResolver: $this->createMock(RelationResolver::class),
                logger: $this->createMock(LoggerInterface::class),
            )
        ;

        parent::setUp();
    }
}
