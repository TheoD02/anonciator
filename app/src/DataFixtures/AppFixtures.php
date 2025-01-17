<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Tests\Factory\AnnounceCategoryFactory;
use App\Tests\Factory\AnnounceFactory;
use App\Tests\Factory\UserFactory;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function Zenstruck\Foundry\Persistence\flush_after;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        ini_set('memory_limit', '2G');

        $user = UserFactory::new()
            ->afterInstantiate(
                fn (User $user): User => $user->setPassword($this->passwordHasher->hashPassword($user, 'dummy'))
            )
            ->create([
                'username' => 'Dummy User',
                'email' => 'dummy@domain.tld',
            ])
        ;

        AnnounceCategoryFactory::new()->many(6)->create();
        // Create announce as dummy user to allow conversation from admin user
        flush_after(static fn (): array => AnnounceFactory::new()->createMany(100, [
            'createdBy' => $user->getEmail(),
        ]));

        UserFactory::admin();
    }
}
