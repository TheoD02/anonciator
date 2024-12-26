<?php

namespace App\Tests\Announce\Controller;

use App\Announce\Controller\CreateAnnounceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceCategoryFactory;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateAnnounceController::class)]
class CreateAnnounceControllerTest extends AbstractApiWebTestCase
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
        $category = AnnounceCategoryFactory::new()->create();

        // Act
        $this->request('POST', json: [
            'title' => 'Title',
            'description' => 'Description',
            'price' => 100,
            'category' => ['set' => [$category->getId()]],
            'location' => '41.0987',
            'status' => 'draft',
            'photos' => ['set' => []],
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
