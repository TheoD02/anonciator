<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller;

use App\Announce\AnnounceStatus;
use App\Announce\Controller\PartialUpdateAnnounceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(PartialUpdateAnnounceController::class)]
final class PartialUpdateAnnounceControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return PartialUpdateAnnounceController::class;
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
        $this->request('PATCH', parameters: [
            'id' => 1,
        ], json: [
            'title' => 'new title',
        ]);

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }

    public function testWhenNotFound(): void
    {
        // Act
        $this->request('PATCH', parameters: [
            'id' => 1,
        ], json: [
            'title' => 'new title',
        ]);

        // Assert
        self::assertResponseStatusCodeSame(404);
        $this->assertJsonResponseFile();
    }

    public function testPatchingNothing(): void
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
        $this->request('PATCH', parameters: [
            'id' => 1,
        ], json: []);

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }
}
