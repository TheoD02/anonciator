<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Announce;

use App\Announce\AnnounceStatus;
use App\Announce\Controller\Announce\UpdateAnnounceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceCategoryFactory;
use App\Tests\Factory\AnnounceFactory;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(UpdateAnnounceController::class)]
final class UpdateAnnounceControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return UpdateAnnounceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces/{id}';
    }

    public function testOk(): void
    {
        // Arrange
        $category = AnnounceCategoryFactory::findOrCreate([
            'name' => 'Category',
        ]);
        $announce = AnnounceFactory::new()->create([
            'title' => 'Title',
            'description' => 'Description',
            'price' => 100,
            'category' => $category,
            'location' => '41.0987',
            'status' => AnnounceStatus::DRAFT,
        ])->_real();

        // Act
        $this->request('PUT', parameters: [
            'id' => 1,
        ], json: [
            'title' => 'new title',
            'description' => 'new description',
            'price' => 200,
            'category' => [
                'set' => [$announce->getCategory()->getId()],
            ],
            'location' => '41.0987',
            'status' => AnnounceStatus::PUBLISHED,
        ]);

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }

    public function testFullValidationFailed(): void
    {
        // Act
        $this->request('PUT', parameters: [
            'id' => 1,
        ], json: []);

        // Assert
        self::assertResponseStatusCodeSame(422);
        $this->assertJsonResponseFile();
    }
}
