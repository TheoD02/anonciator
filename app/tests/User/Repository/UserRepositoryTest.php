<?php

declare(strict_types=1);

namespace App\Tests\User\Repository;

use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;

/**
 * @internal
 */
#[CoversClass(UserRepository::class)]
final class UserRepositoryTest extends TestCase
{
    use Factories;

    public function testGetEntityFqcn(): void
    {
        $repository = new UserRepository($this->createMock(ManagerRegistry::class));
        self::assertSame(User::class, $repository->getEntityFqcn());
    }
}
