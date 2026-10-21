<?php

declare(strict_types=1);

namespace App\Tests\User\Service;

use App\Shared\Api\RelationResolver;
use App\Tests\Factory\UserFactory;
use App\User\Entity\User;
use App\User\Repository\UserRepository;
use App\User\Service\UserService;
use AutoMapper\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
#[CoversClass(UserService::class)]
final class UserServiceTest extends TestCase
{
    private UserService $userService;

    private UserRepository&MockObject $userRepositoryMock;

    public function testGetOneByEmail(): void
    {
        // Arrange
        $user = UserFactory::new()->create();

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'email' => $user->getEmail(),
            ])
            ->willReturn($user)
        ;

        // Act
        $user = $this->userService->getOneByEmail($user->getEmail());

        // Assert
        self::assertInstanceOf(User::class, $user);
    }

    public function testGetOneByEmailWithNonExistingUser(): void
    {
        // Arrange
        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        // Act
        $user = $this->userService->getOneByEmail('unknown@mail.com');

        // Assert
        self::assertNull($user);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $emMock = $this->createMock(EntityManagerInterface::class);
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $emMock->method('getRepository')->willReturn($this->userRepositoryMock);

        $this->userService = new UserService();
        $this->userService
            ->setEntityCrudServiceDependencies(
                mapper: $this->createStub(AutoMapperInterface::class),
                em: $emMock,
                dispatcher: $this->createStub(EventDispatcherInterface::class),
                relationResolver: $this->createStub(RelationResolver::class),
                logger: $this->createStub(LoggerInterface::class),
            )
        ;
    }
}
