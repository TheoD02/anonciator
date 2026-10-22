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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use function Zenstruck\Foundry\set;

/**
 * @internal
 */
#[CoversClass(MessageService::class)]
final class MessageServiceTest extends TestCase
{
    /**
     * @var MockObject&ConversationService
     */
    public MockObject $conversationServiceMock;

    /**
     * @var MockObject&UserService
     */
    public MockObject $userServiceMock;

    public $messageService;

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
        $conversation = set(ConversationFactory::new()
            ->create([
                'announce' => $announce,
                'initializedBy' => $loggedUser,
                'receiver' => $announceCreator,
            ]), 'id', 1);

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
        self::assertSame($messagePayload->content, $message->getContent());
        self::assertSame($loggedUser->getUserIdentifier(), $message->getSentBy());
        self::assertSame($announceCreator->getUserIdentifier(), $message->getSentTo());
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
        $conversation = set(ConversationFactory::new()
            ->create([
                'announce' => $announce,
                'initializedBy' => $loggedUser,
                'receiver' => $announceCreator,
            ]), 'id', 1);

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
            loggedUser: $announceCreator,
        );

        // Assert
        self::assertSame($messagePayload->content, $message->getContent());
        self::assertSame($loggedUser->getUserIdentifier(), $message->getSentTo());
        self::assertSame($announceCreator->getUserIdentifier(), $message->getSentBy());
    }

    protected function setUp(): void
    {
        $this->conversationServiceMock = $this->createMock(ConversationService::class);
        $this->userServiceMock = $this->createMock(UserService::class);

        $this->messageService = new MessageService(
            conversationService: $this->conversationServiceMock,
            announceService: $this->createStub(AnnounceService::class),
            userService: $this->userServiceMock,
        );

        $this->messageService
            ->setEntityCrudServiceDependencies(
                mapper: $this->createStub(AutoMapperInterface::class),
                em: $this->createStub(EntityManagerInterface::class),
                dispatcher: $this->createStub(EventDispatcherInterface::class),
                relationResolver: $this->createStub(RelationResolver::class),
                logger: $this->createStub(LoggerInterface::class),
            )
        ;

        parent::setUp();
    }
}
