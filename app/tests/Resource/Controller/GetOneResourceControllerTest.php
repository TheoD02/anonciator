<?php

declare(strict_types=1);

namespace App\Tests\Resource\Controller;

use App\Resource\Controller\GetOneResourceController;
use App\Tests\AbstractApiWebTestCase;
use App\Tests\Factory\ResourceFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @internal
 */
#[CoversClass(GetOneResourceController::class)]
class GetOneResourceControllerTest extends AbstractApiWebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testOk(): void
    {
        // Arrange
        $kernelProjectDir = self::getContainer()->getParameter('kernel.project_dir');

        $filename = 'abc1.txt';
        $filepath = "{$kernelProjectDir}/public/uploads/{$filename}";
        $fileContent = 'abc1';
        file_put_contents($filepath, $fileContent);

        ResourceFactory::new()->create([
            'bucket' => 'files',
            'path' => '/uploads/abc1.txt',
            'originalName' => 'abc-1.jpg',
        ]);

        // Act
        $this->request(Request::METHOD_GET, parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseIsSuccessful();
        self::assertSame($fileContent, self::$client->getInternalResponse()->getContent());

        // Clean up
        unlink($filepath);
    }

    public function testNotFound(): void
    {
        // Act
        $this->request(Request::METHOD_GET, parameters: [
            'id' => 1,
        ]);

        // Assert
        self::assertResponseStatusCodeSame(404);
        $this->assertJsonResponseFile();
    }

    public function getAction(): string
    {
        return GetOneResourceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/resources/{id}';
    }
}
