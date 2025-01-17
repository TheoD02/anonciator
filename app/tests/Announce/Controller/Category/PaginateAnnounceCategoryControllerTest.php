<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Category;

use App\Announce\Controller\Category\PaginateAnnounceCategoryController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceCategoryFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(PaginateAnnounceCategoryController::class)]
class PaginateAnnounceCategoryControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return PaginateAnnounceCategoryController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces/categories';
    }

    public function testOk(): void
    {
        // Arrange
        AnnounceCategoryFactory::new()->sequence([
            [
                'name' => 'Category 1',
            ],
            [
                'name' => 'Category 2',
            ],
            [
                'name' => 'Category 3',
            ],
        ])->create();

        // Act
        $this->request(Request::METHOD_GET);

        // Assert
        self::assertResponseStatusCodeSame(200);
        self::assertJsonResponseFile();
    }

    public function testEmpty(): void
    {
        // Act
        $this->request(Request::METHOD_GET);

        // Assert
        self::assertResponseStatusCodeSame(200);
        self::assertJsonResponseFile();
    }
}
