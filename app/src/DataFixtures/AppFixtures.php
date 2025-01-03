<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Tests\Factory\AnnounceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\Persistence\flush_after;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ini_set('memory_limit', '2G');
        flush_after(static fn(): array => AnnounceFactory::new()->createMany(100));

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
