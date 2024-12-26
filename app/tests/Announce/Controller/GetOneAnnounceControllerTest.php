<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller;

use App\Announce\AnnounceStatus;
use App\Announce\Controller\GetOneAnnounceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(GetOneAnnounceController::class)]
final class GetOneAnnounceControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return GetOneAnnounceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces/{id}';
    }

    public function testOk(): void
    {
        // Arrange
        AnnounceFactory::new()->create([
            'title' => 'Title',
            'description' => 'Description',
            'price' => 100,
            'location' => '41.0987',
            'status' => AnnounceStatus::DRAFT,
        ]);

        // Act
        $this->request('GET', parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }
}
