<?php

declare(strict_types=1);

namespace App\Tests\Shared\Trait;

use App\Announce\Dto\Payload\CreateAnnouncePayload;
use App\Announce\Dto\Payload\UpdateAnnouncePayload;
use App\Announce\Entity\Announce;
use App\Announce\Enum\AnnounceStatus;
use App\Announce\Repository\AnnounceRepository;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\Relation;
use App\Shared\Exception\GenericDomainModelNotFoundException;
use App\Tests\Factory\AnnounceCategoryFactory;
use App\Tests\Factory\AnnounceFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @internal
 */
final class EntityCrudServiceTraitTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public AnnounceService $instance;

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
        $entity = AnnounceFactory::new()->create()->_real();

        // Act
        $result = $this->instance->createEntity($entity, flush: true);

        // Assert
        AnnounceFactory::repository()->assert()->exists($result->getId());
    }

    public function testUpdateEntityFromPayload(): void
    {
        // Arrange
        $announce = AnnounceFactory::new()->create();
        $payload = new UpdateAnnouncePayload();
        $payload->title = 'Updated title';
        $payload->description = 'Updated description';
        $payload->status = AnnounceStatus::DRAFT;
        $payload->price = '100';
        $payload->location = '10.0000';
        $payload->category = new Relation(set: [$announce->getCategory()->getId()]);

        // Act
        $result = $this->instance->updateEntityFromPayload($announce->getId(), $payload);

        // Assert
        AnnounceFactory::repository()->assert()->exists($result->getId());
        self::assertEquals('Updated title', $result->getTitle());
        self::assertEquals('Updated description', $result->getDescription());
    }

    public function testUpdateEntity(): void
    {
        // Arrange
        $announce = AnnounceFactory::new()->create();

        $announce->setTitle('Updated title');

        // Act
        $result = $this->instance->updateEntity($announce->_real(), flush: true);

        // Assert
        AnnounceFactory::repository()->assert()->exists($result->getId());
        self::assertEquals('Updated title', $result->getTitle());
    }

    public function testGetEntityById(): void
    {
        // Arrange
        $announce = AnnounceFactory::new()->create();

        // Act
        $result = $this->instance->getEntityById($announce->getId());

        // Assert
        self::assertInstanceOf(Announce::class, $result);
    }

    public function testNonExistingGetEntityById(): void
    {
        // Assert
        $this->expectException(GenericDomainModelNotFoundException::class);

        // Act
        $this->instance->getEntityById(1, fail: true);
    }

    public function testGetRepository(): void
    {
        // Act
        $result = $this->instance->getRepository();

        // Assert
        self::assertSame(AnnounceRepository::class, $result::class);
        self::assertSame(Announce::class, $result->getClassName());
    }

    public function testPartialUpdateEntityFromPayload(): void
    {
        // Arrange
        $announce = AnnounceFactory::new()->create();
        $payload = new UpdateAnnouncePayload();
        $payload->title = 'Updated title';

        // Act
        $result = $this->instance->partialUpdateEntityFromPayload($announce->getId(), $payload);

        // Assert
        AnnounceFactory::repository()->assert()->exists($result->getId());
        self::assertEquals($announce->getDescription(), $result->getDescription());
        self::assertEquals('Updated title', $result->getTitle());
    }

    public function testDeleteEntityFromId(): void
    {
        // Arrange
        $announce = AnnounceFactory::new()->create();

        // Act
        $this->instance->deleteEntityFromId($announce->getId());

        // Assert
        AnnounceFactory::repository()->assert()->empty();
    }

    public function testDeleteEntity(): void
    {
        // Arrange
        $announce = AnnounceFactory::new()->create();

        // Act
        $this->instance->deleteEntity($announce->_real());

        // Assert
        AnnounceFactory::repository()->assert()->empty();
    }

    public function testPaginateEntities(): void
    {
        // Arrange
        AnnounceFactory::new()->many(10)->create();

        // Act
        $result = $this->instance->paginateEntities();

        // Assert
        self::assertCount(10, $result->getIterator());
    }

    public function testEmptyPaginateEntities(): void
    {
        // Act
        $result = $this->instance->paginateEntities();

        // Assert
        self::assertCount(0, $result->getIterator());
    }

    protected function setUp(): void
    {
        $this->instance = self::getContainer()->get(AnnounceService::class);
        parent::setUp();
    }
}
