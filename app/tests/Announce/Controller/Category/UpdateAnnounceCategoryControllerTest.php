<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Category;

use App\Announce\Controller\Category\UpdateAnnounceCategoryController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceCategoryFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(UpdateAnnounceCategoryController::class)]
class UpdateAnnounceCategoryControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return UpdateAnnounceCategoryController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces/categories/{id}';
    }

    public function testOk(): void
    {
        // Arrange
        $category = AnnounceCategoryFactory::new()->create();

        // Act
        $this->request(
            method: Request::METHOD_PUT,
            parameters: [
                'id' => $category->getId(),
            ],
            json: [
                'name' => 'New category name',
            ]
        );

        // Assert
        self::assertResponseStatusCodeSame(200);
        self::assertJsonResponseFile();
    }

    public function testNotFound(): void
    {
        // Act
        $this->request(
            method: Request::METHOD_PUT,
            parameters: [
                'id' => 1,
            ],
            json: [
                'name' => 'New category name',
            ]
        );

        // Assert
        self::assertResponseStatusCodeSame(404);
        $this->assertJsonResponseFile();
    }
}
