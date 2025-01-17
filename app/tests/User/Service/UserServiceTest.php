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
use Zenstruck\Foundry\Test\Factories;

/**
 * @internal
 */
#[CoversClass(UserService::class)]
class UserServiceTest extends TestCase
{
    use Factories;

    private UserService $userService;
    private EntityManagerInterface&MockObject $emMock;
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
        $this->assertInstanceOf(User::class, $user);
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
        $this->assertNull($user);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->emMock->method('getRepository')->willReturn($this->userRepositoryMock);

        $this->userService = new UserService();
        $this->userService
            ->setEntityCrudServiceDependencies(
                mapper: $this->createMock(AutoMapperInterface::class),
                em: $this->emMock,
                dispatcher: $this->createMock(EventDispatcherInterface::class),
                relationResolver: $this->createMock(RelationResolver::class),
                logger: $this->createMock(LoggerInterface::class),
            )
        ;
    }
}
