<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Category;

use App\Announce\Controller\Category\CreateAnnounceCategoryController;
use App\Tests\AbstractApiWebTestCase;

/**
 * @internal
 */
final class CreateAnnounceCategoryControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return CreateAnnounceCategoryController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces/categories';
    }

    public function testOk(): void
    {
        // Act
        $this->request('POST', json: [
            'name' => 'Category name',
            'description' => 'Category description',
        ]);

        // Assert
        self::assertResponseStatusCodeSame(201);
        self::assertJsonResponseFile();
    }

    public function testEmptyPayload(): void
    {
        // Act
        $this->request('POST', json: []);

        // Assert
        self::assertResponseStatusCodeSame(422);
        self::assertJsonResponseFile();
    }
}
