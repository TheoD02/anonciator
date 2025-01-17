<?php

declare(strict_types=1);

namespace App\Tests\Conversation\Service;

use App\Announce\Service\AnnounceService;
use App\Conversation\Builder\ConversationBuilder;
use App\Conversation\Repository\ConversationRepository;
use App\Conversation\Service\ConversationService;
use App\Shared\Api\RelationResolver;
use App\Tests\Factory\AnnounceFactory;
use App\Tests\Factory\UserFactory;
use App\User\Service\UserService;
use AutoMapper\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Zenstruck\Foundry\Test\Factories;

/**
 * @internal
 */
#[CoversClass(ConversationService::class)]
final class ConversationServiceTest extends TestCase
{
    use Factories;

    private UserService&MockObject $userServiceMock;

    private AnnounceService&MockObject $announceServiceMock;

    private ConversationRepository&MockObject $conversationRepositoryMock;

    private EntityManagerInterface&MockObject $emMock;

    private ConversationService $conversationService;

    public function testInitConversationWithoutExistingConversation(): void
    {
        // Arrange
        $userAnnounceCreator = UserFactory::new()->create()->_set('id', 1);
        $loggedUser = UserFactory::new()->create()->_set('id', 2);
        $announce = AnnounceFactory::new()
            ->withCreator($userAnnounceCreator->_real())
            ->create()
            ->_set('id', 1)
        ;

        $this->announceServiceMock
            ->expects($this->once())
            ->method('getEntityById')
            ->with(1, true)
            ->willReturn($announce)
        ;

        $this->userServiceMock
            ->expects($this->once())
            ->method('getOneByEmail')
            ->with($userAnnounceCreator->getEmail())
            ->willReturn($userAnnounceCreator)
        ;

        $this->conversationRepositoryMock
            ->expects($this->once())
            ->method('getConversationMatchingAnnounceAndUser')
            ->with(1, $loggedUser->getId())
            ->willReturn(null)
        ;

        $this
            ->emMock
            ->expects($this->once())
            ->method('persist')
        ;

        $this
            ->emMock
            ->expects($this->once())
            ->method('flush')
        ;

        // Act
        $conversation = $this->conversationService->initConversationOrGetExisting(
            announceId: $announce->getId(),
            loggedUser: $loggedUser->_real()
        );

        // Assert
        self::assertNotNull($conversation);
    }

    public function testInitConversationWhenAnnounceCreatorDoesNotExist(): void
    {
        // Arrange
        $loggedUser = UserFactory::new()->create()->_set('id', 2);
        $announce = AnnounceFactory::new()->create()->_set('id', 1);

        $this->announceServiceMock
            ->expects($this->once())
            ->method('getEntityById')
            ->with(1, true)
            ->willReturn($announce)
        ;

        $this->userServiceMock
            ->expects($this->once())
            ->method('getOneByEmail')
            ->with($announce->getCreatedBy())
            ->willReturn(null)
        ;

        // Assert
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Announce creator does not exist');

        // Act
        $this->conversationService->initConversationOrGetExisting(
            announceId: $announce->getId(),
            loggedUser: $loggedUser->_real()
        );
    }

    public function testInitConversationWhenLoggedUserIsAnnounceCreator(): void
    {
        // Arrange
        $userAnnounceCreator = UserFactory::new()->create()->_set('id', 1);
        $announce = AnnounceFactory::new()
            ->withCreator($userAnnounceCreator->_real())
            ->create()
            ->_set('id', 1)
        ;

        $this->announceServiceMock
            ->expects($this->once())
            ->method('getEntityById')
            ->with(1, true)
            ->willReturn($announce)
        ;

        $this->userServiceMock
            ->expects($this->once())
            ->method('getOneByEmail')
            ->with($userAnnounceCreator->getEmail())
            ->willReturn($userAnnounceCreator)
        ;

        $this->expectException(UnprocessableEntityHttpException::class);
        $this->expectExceptionMessage('Cannot create conversation to self');

        // Act
        $this->conversationService->initConversationOrGetExisting(
            announceId: $announce->getId(),
            loggedUser: $userAnnounceCreator->_real()
        );
    }

    public function testInitConversationWithExistingConversation(): void
    {
        // Arrange
        $userAnnounceCreator = UserFactory::new()->create()->_set('id', 1);
        $loggedUser = UserFactory::new()->create()->_set('id', 2);
        $announce = AnnounceFactory::new()
            ->withCreator($userAnnounceCreator->_real())
            ->create()
            ->_set('id', 1)
        ;
        $existingConversation = ConversationBuilder::new()
            ->withAnnounce($announce)
            ->withInitializedBy($loggedUser->_real())
            ->withReceiver($userAnnounceCreator->_real())
            ->build()
        ;

        $this->announceServiceMock
            ->expects($this->once())
            ->method('getEntityById')
            ->with(1, true)
            ->willReturn($announce)
        ;

        $this->userServiceMock
            ->expects($this->once())
            ->method('getOneByEmail')
            ->with($userAnnounceCreator->getEmail())
            ->willReturn($userAnnounceCreator)
        ;

        $this->conversationRepositoryMock
            ->expects($this->once())
            ->method('getConversationMatchingAnnounceAndUser')
            ->with(1, $loggedUser->getId())
            ->willReturn($existingConversation)
        ;

        $this
            ->emMock
            ->expects($this->never())
            ->method('persist')
        ;

        $this
            ->emMock
            ->expects($this->never())
            ->method('flush')
        ;

        // Act
        $conversation = $this->conversationService->initConversationOrGetExisting(
            announceId: $announce->getId(),
            loggedUser: $loggedUser->_real()
        );

        // Assert
        self::assertSame($existingConversation, $conversation);
    }

    protected function setUp(): void
    {
        $this->userServiceMock = $this->createMock(UserService::class);
        $this->announceServiceMock = $this->createMock(AnnounceService::class);
        $this->conversationRepositoryMock = $this->createMock(ConversationRepository::class);

        $this->conversationService = new ConversationService(
            announceService: $this->announceServiceMock,
            userService: $this->userServiceMock,
            conversationRepository: $this->conversationRepositoryMock,
        );

        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->conversationService
            ->setEntityCrudServiceDependencies(
                mapper: $this->createMock(AutoMapperInterface::class),
                em: $this->emMock,
                dispatcher: $this->createMock(EventDispatcherInterface::class),
                relationResolver: $this->createMock(RelationResolver::class),
                logger: $this->createMock(LoggerInterface::class),
            )
        ;

        parent::setUp();
    }
}
