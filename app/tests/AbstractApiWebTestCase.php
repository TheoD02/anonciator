<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\Factory\UserFactory;
use App\User\Entity\User;
use PHPUnit\Framework\Attributes\Before;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

use function Symfony\Component\String\u;

/** @phpstan-ignore-next-line shipmonk.invalidClassSuffix (OK for abstract class) */
abstract class AbstractApiWebTestCase extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    protected static KernelBrowser $client;

    protected ?User $user = null;

    public static function getRequest(): Request
    {
        return self::$client->getRequest();
    }

    /**
     * @before
     */
    public function setupClient(): void
    {
        self::$client = static::createClient([], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ]);
    }

    public function testExpectedUrl(): void
    {
        $action = $this->getAction();

        /** @var RouterInterface $router */
        $router = self::getContainer()->get(RouterInterface::class);

        $route = $router->getRouteCollection()->get($action);

        if ($route === null) {
            throw new \RuntimeException('No route found');
        }

        self::assertSame($this->expectedUrl(), u($route->getPath())->ensureStart('/')->trimEnd('/')->toString());
    }

    /**
     * @return class-string
     */
    abstract public function getAction(): string;

    abstract public function expectedUrl(): string;

    public function upload(string $filename, string $mimeType = 'application/octet-stream'): void
    {
        file_put_contents($filename, 'test');
        $uploadedFile = new UploadedFile(
            $filename,
            'test-file.txt',
            'text/plain',
            null,
            true // Set the last parameter to true to indicate this file was uploaded
        );

        $headers = [
            'HTTP_ACCEPT' => 'application/json',
        ];

        $this->initToken();

        self::$client->request(
            method: Request::METHOD_POST,
            uri: $this->getUrl(),
            files: [
                'file' => $uploadedFile,
            ],
            server: $headers,
        );

        unlink($filename);
    }

    private function initToken(): void
    {
        if (! $this->user instanceof User) {
            $this->user = UserFactory::admin();
        }

        $jwtTokenPath = 'var/cache/test/.jwt_token';
        if (file_exists($jwtTokenPath)) {
            $token = file_get_contents($jwtTokenPath);
            self::$client->setServerParameter('HTTP_AUTHORIZATION', \sprintf('Bearer %s', $token));

            return;
        }

        self::$client->jsonRequest(
            'POST',
            '/api/login_check',
            [
                'username' => $this->user->getEmail(),
                'password' => 'admin',
            ]
        );

        $data = json_decode(self::$client->getResponse()->getContent(), true);

        self::$client->setServerParameter('HTTP_AUTHORIZATION', \sprintf('Bearer %s', $data['token']));

        file_put_contents($jwtTokenPath, $data['token']);
    }

    /**
     * @return Response
     */
    public static function getResponse(bool $json = true): Response|array
    {
        if ($json) {
            try {
                /** @var array<mixed> */
                return json_decode(
                    self::$client->getResponse()->getContent() ?: '',
                    true,
                    512,
                    \JSON_THROW_ON_ERROR
                );
            } catch (\JsonException $e) {
                throw new \InvalidArgumentException(
                    'Invalid JSON data received from the response',
                    $e->getCode(),
                    previous: $e
                );
            }
        }

        return self::$client->getResponse();
    }

    /**
     * @param array<string, int|string>  $parameters
     * @param array<mixed>|null          $json
     * @param array<string, string>|null $headers
     * @param array<string, mixed>|null  $query
     */
    public function request(
        string $method,
        array $parameters = [],
        ?array $json = null,
        ?array $headers = null,
        ?array $query = null,
        bool $noParse = false,
    ): Crawler {
        $this->initToken();
        $uri = $this->buildUrl($parameters);
        if ($query !== null) {
            $uri .= '?' . http_build_query($query);
        }

        if ($headers === null) {
            $headers = [];
        }

        $headers = array_merge(
            $headers,
            [
                'HTTP_ACCEPT' => 'application/json, application/octet-stream',
                'CONTENT_TYPE' => 'application/json',
            ],
        );

        if ($json !== null) {
            if (! \in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
                throw new \InvalidArgumentException('JSON data can only be sent with POST, PUT or PATCH');
            }

            try {
                return self::$client->request(
                    method: $method,
                    uri: $uri,
                    server: $headers,
                    content: json_encode($json, \JSON_THROW_ON_ERROR),
                );
            } catch (\JsonException $e) {
                throw new \InvalidArgumentException('Invalid JSON data provided', $e->getCode(), previous: $e);
            }
        }

        return self::$client->request(method: $method, uri: $uri, server: $headers);
    }

    /**
     * @param array<string, int|string> $parameters
     */
    private function buildUrl(array $parameters = []): string
    {
        $url = $this->getUrl($parameters);

        foreach ($parameters as $key => $value) {
            $url = str_replace("{{$key}}", (string) $value, $url);
        }

        return $url;
    }

    /**
     * @param array<string, int|string> $parameters
     */
    private function getUrl(array $parameters = []): string
    {
        $action = $this->getAction();

        /** @var RouterInterface $router */
        $router = self::getContainer()->get(RouterInterface::class);

        $route = $router->getRouteCollection()->get($action);

        if ($route === null) {
            throw new \RuntimeException('No route found');
        }

        $compiledRoute = $route->compile();

        $pathRequirements = $compiledRoute->getPathVariables();
        if ($pathRequirements !== array_keys($parameters)) {
            $missing = array_diff($pathRequirements, array_keys($parameters));
            throw new \InvalidArgumentException('Missing parameters: ' . implode(', ', $missing));
        }

        return u($router->generate($action, $parameters, UrlGeneratorInterface::RELATIVE_PATH))
            ->ensureStart('/')
            ->ensureStart('/api')
            ->trimEnd('/')
            ->toString()
        ;
    }

    protected function assertJsonResponseFile(array $replacers = []): void
    {
        $response = self::getResponse(json: false);

        $fs = new Filesystem();

        $filepath = $this->getFilePath();
        $directory = \dirname($filepath);

        if ($fs->exists($directory) === false) {
            $fs->mkdir($directory, 0o777);
        }

        $responseContent = $response->getContent() ?: '';
        $isFileExists = $fs->exists($filepath);

        if ($response->getStatusCode() === Response::HTTP_NO_CONTENT) {
            if ($responseContent !== '') {
                self::fail('Response content should be empty');
            }

            if ($isFileExists) {
                $fs->remove($filepath);
            }

            return;
        }

        $replacers = [
            '/"id":\s*"[0-9a-f]{24}"/' => '"id": "auto-generated-id"',
            '/"createdAt":\s*"[0-9T:+-]+"/' => '"createdAt": "auto-generated-date"',
            '/"updatedAt":\s*"[0-9T:+-]+"/' => '"updatedAt": "auto-generated-date"',
            '/"lastUpdatedAt":\s*"[0-9T:+-]+"/' => '"updatedAt": "auto-generated-date"',
            '/"deletedAt":\s*"[0-9T:+-]+"/' => '"deletedAt": "auto-generated-date"',
            '/"sentAt":\s*"[0-9T:+-]+"/' => '"sentAt": "auto-generated-date"',
            ...$replacers,
        ];

        $responseContent = preg_replace(array_keys($replacers), array_values($replacers), $responseContent);

        if (! \is_string($responseContent)) {
            throw new \RuntimeException('Invalid JSON data received from the response');
        }

        if ($isFileExists === false) {
            $fs->dumpFile($filepath, self::prettifyJson($responseContent));
        }

        $fileContent = file_get_contents($filepath) ?: '';
        $fileData = self::prettifyJson($fileContent);

        self::assertSame($fileData, self::prettifyJson($responseContent));
    }

    private function getFilePath(string $suffix = ''): string
    {
        $reflection = new \ReflectionClass(static::class);
        $expectedFile = $reflection->getFileName();

        if ($expectedFile === false) {
            throw new \RuntimeException('Unable to determine file');
        }

        return \sprintf(
            '%s%s%s%s%s',
            \dirname($expectedFile),
            \DIRECTORY_SEPARATOR,
            $reflection->getShortName(),
            \DIRECTORY_SEPARATOR,
            u($this->name())->append($suffix)->camel()->append('.json')->toString(),
        );
    }

    protected static function prettifyJson(string $content): string
    {
        $jsonFlags = \JSON_PRETTY_PRINT;
        if (! isset($_SERVER['ESCAPE_JSON']) || $_SERVER['ESCAPE_JSON'] !== true) {
            $jsonFlags |= \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES;
        }

        try {
            /** @var false|non-empty-string $encodedContent */
            $encodedContent = json_encode(
                json_decode($content, true, 512, \JSON_THROW_ON_ERROR),
                \JSON_THROW_ON_ERROR | $jsonFlags,
            );

            if ($encodedContent === false) {
                throw new \InvalidArgumentException('Invalid JSON data provided when trying to prettify it');
            }
        } catch (\JsonException $jsonException) {
            throw new \InvalidArgumentException(
                'Invalid JSON data provided when trying to prettify it',
                $jsonException->getCode(),
                previous: $jsonException
            );
        }

        return $encodedContent;
    }

    protected function getPayload(): array
    {
        $filepath = $this->getFilePath('Payload');

        if (! file_exists($filepath)) {
            if (! file_exists(\dirname($filepath))) {
                mkdir(\dirname($filepath), 0o777, true);
            }

            file_put_contents($filepath, self::prettifyJson('{}'));
        }

        return json_decode(file_get_contents($filepath), true, 512, \JSON_THROW_ON_ERROR);
    }
}
