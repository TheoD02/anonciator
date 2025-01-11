<?php

namespace App\Tests\Shared\Trait;

use App\Announce\AnnounceStatus;
use App\Announce\Dto\Payload\CreateAnnouncePayload;
use App\Announce\Entity\Announce;
use App\Announce\Service\AnnounceCategoryService;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\Relation;
use App\Tests\Factory\AnnounceCategoryFactory;
use App\Tests\Factory\AnnounceFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class EntityCrudServiceTraitTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        $this->instance = self::getContainer()->get(AnnounceService::class);
        self::_resetDatabaseBeforeEachTest();
        parent::setUp();
    }

    public function testCreateEntityFromPayload(): void
    {
        // Arrange
        $announceCategory = AnnounceCategoryFactory::new()->create();
        $payload = new CreateAnnouncePayload();
        $payload->title = 'Test title';
        $payload->description = 'Test description';
        $payload->category = new Relation(set: [$announceCategory->getId()]);
        $payload->status = AnnounceStatus::DRAFT;
        $payload->price = '100';
        $payload->location = '10.0000';

        // Act
        $result = $this->instance->createEntityFromPayload($payload);

        // Assert
        AnnounceFactory::repository()->assert()->count(1);
        AnnounceFactory::repository()->assert()->exists($result->getId());
    }

    public function testCreateEntityWithFlush(): void
    {
        // Arrange
        $category = AnnounceCategoryFactory::new()->create()->_real();
        $entity = AnnounceFactory::new()->withoutPersisting()->create([
            'category' => $category,
        ])->_real();

        // Act
        $result = $this->instance->createEntity($entity, flush: true);

        // Assert
        AnnounceFactory::repository()->assert()->count(1);
        AnnounceFactory::repository()->assert()->exists($result->getId());

        AnnounceCategoryFactory::repository()->assert()->count(1);
    }
}
