<?php

declare(strict_types=1);

namespace App\Tests\Resource\Controller;

use App\Resource\Controller\UploadResourceController;
use App\Tests\AbstractApiWebTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(UploadResourceController::class)]
class UploadResourceControllerTest extends AbstractApiWebTestCase
{
    public function testOk(): void
    {
        // Act
        $kernelProjectDir = self::getContainer()->getParameter('kernel.project_dir');
        $this->upload('test.txt', 'text/plain');

        // Assert
        self::assertResponseIsSuccessful();
        $this->assertJsonResponseFile([
            '/"path":\s*"(.*?)"/' => '"path": "/uploads/random-name.txt"',
        ]);

        $response = self::getResponse();
        $filepath = "{$kernelProjectDir}/public{$response['data']['path']}";
        $this->assertFileExists($filepath);

        // Cleanup
        unlink($filepath);
    }

    public function getAction(): string
    {
        return UploadResourceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/resources';
    }
}
