<?php

namespace App\Tests\Announce\Controller;

use App\Announce\AnnounceStatus;
use App\Announce\Controller\PaginateAnnounceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PaginateAnnounceController::class)]
class PaginateAnnounceControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return PaginateAnnounceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces';
    }

    public function testOk(): void
    {
        // Arrange
        AnnounceFactory::new()->sequence([
            [
                'title' => 'Title 1',
                'description' => 'Description 1',
                'price' => 100,
                'location' => '41.0987',
                'status' => AnnounceStatus::DRAFT,
            ],
            [
                'title' => 'Title 2',
                'description' => 'Description 2',
                'price' => 200,
                'location' => '41.0987',
                'status' => AnnounceStatus::DRAFT,
            ]
        ])->create();

        // Act
        $this->request('GET');

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }

    public function testEmpty(): void
    {
        // Act
        $this->request('GET');

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }
}
