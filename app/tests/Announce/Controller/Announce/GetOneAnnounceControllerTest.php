<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Announce;

use App\Announce\Controller\Announce\GetOneAnnounceController;
use App\Announce\Enum\AnnounceStatus;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;
use App\Tests\Factory\UserFactory;
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
            'createdBy' => UserFactory::admin()->getUserIdentifier(),
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
