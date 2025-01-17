<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Announce;

use App\Announce\Controller\Announce\PaginateAnnounceController;
use App\Announce\Enum\AnnounceStatus;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(PaginateAnnounceController::class)]
final class PaginateAnnounceControllerTest extends AbstractApiWebTestCase
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
            ],
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
