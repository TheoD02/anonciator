<?php

namespace App\Tests\Resource\Controller;

use App\Resource\Controller\DeleteResourceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\ResourceFactory;

class DeleteResourceControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return DeleteResourceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/resource/{id}';
    }

    public function testOk(): void
    {
        // Arrange
        $resource = ResourceFactory::new()->create();

        // Act
        $this->request('DELETE', parameters: ['id' => $resource->getId()]);

        // Assert
        self::assertResponseStatusCodeSame(204);
    }
}
