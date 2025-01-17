<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Announce;

use App\Announce\Controller\Announce\CreateAnnounceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceCategoryFactory;
use App\Tests\Factory\ResourceFactory;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(CreateAnnounceController::class)]
final class CreateAnnounceControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return CreateAnnounceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces';
    }

    public function testOk(): void
    {
        // Arrange
        ResourceFactory::new()->create();
        AnnounceCategoryFactory::new()->create();

        // Act
        $this->request('POST', json: [
            'title' => 'Title',
            'description' => 'Description',
            'price' => 100,
            'category' => [
                'set' => [1],
            ],
            'location' => '41.0987',
            'status' => 'draft',
            'photos' => [
                'set' => [1],
            ],
        ]);

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }

    public function testFullValidationFailed(): void
    {
        // Act
        $this->request('POST', json: []);

        // Assert
        self::assertResponseStatusCodeSame(422);
        $this->assertJsonResponseFile();
    }
}
