<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Category;

use App\Announce\Controller\Category\GetOneAnnounceCategoryController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceCategoryFactory;

/**
 * @internal
 */
final class GetOneAnnounceCategoryControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return GetOneAnnounceCategoryController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces/categories/{id}';
    }

    public function testOk(): void
    {
        // Arrange
        AnnounceCategoryFactory::new()->create([
            'name' => 'Category 1',
        ]);

        // Act
        $this->authenticate();
        $this->request('GET', parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }
}
