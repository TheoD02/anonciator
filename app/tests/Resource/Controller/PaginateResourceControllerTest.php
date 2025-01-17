<?php

declare(strict_types=1);

namespace App\Tests\Resource\Controller;

use App\Resource\Controller\PaginateResourceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\ResourceFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(PaginateResourceController::class)]
class PaginateResourceControllerTest extends AbstractApiWebTestCase
{
    public function testOk(): void
    {
        // Arrange
        ResourceFactory::new()->sequence([
            [
                'bucket' => 'images',
                'path' => '/uploads/image-abc1.jpg',
                'originalName' => 'image-1.jpg',
            ],
            [
                'bucket' => 'images',
                'path' => '/uploads/image-def2.jpg',
                'originalName' => 'image-2.jpg',
            ],
            [
                'bucket' => 'images',
                'path' => '/uploads/image-ghi3.jpg',
                'originalName' => 'image-3.jpg',
            ],
        ])->create();

        // Act
        $this->request(Request::METHOD_GET);

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }

    public function testEmpty(): void
    {
        // Act
        $this->request(Request::METHOD_GET);

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile();
    }

    public function getAction(): string
    {
        return PaginateResourceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/resources';
    }
}
