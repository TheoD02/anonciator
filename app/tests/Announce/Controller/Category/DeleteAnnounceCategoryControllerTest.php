<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Category;

use App\Announce\Controller\Category\DeleteAnnounceCategoryController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceCategoryFactory;

/**
 * @internal
 */
final class DeleteAnnounceCategoryControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return DeleteAnnounceCategoryController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces/categories/{id}';
    }

    public function testOk(): void
    {
        // Arrange
        AnnounceCategoryFactory::new()->create();

        // Act
        $this->authenticate();
        $this->request('DELETE', parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseStatusCodeSame(204);
    }

    public function testNotFound(): void
    {
        // Act
        $this->request('DELETE', parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseStatusCodeSame(404);
    }
}
