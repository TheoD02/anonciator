<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Announce;

use App\Announce\Controller\Announce\DeleteAnnounceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\AnnounceFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
#[CoversClass(DeleteAnnounceController::class)]
final class DeleteAnnounceControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return DeleteAnnounceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces/{id}';
    }

    public function testOk(): void
    {
        // Arrange
        AnnounceFactory::new()->create();

        // Act
        $this->request(Request::METHOD_DELETE, parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseStatusCodeSame(204);
    }

    public function testNotFound(): void
    {
        // Act
        $this->request(Request::METHOD_DELETE, parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseStatusCodeSame(404);
        $this->assertJsonResponseFile();
    }
}
