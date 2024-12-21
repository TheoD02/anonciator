<?php

declare(strict_types=1);

namespace App\Shared\Api;

use App\Shared\Api\Dto\Adapter\ApiMetaInterface;
use App\Shared\Api\Dto\Response\ErrorResponse;
use App\Shared\Api\Dto\Response\SuccessResponse;
use App\Shared\Api\Mapper\ApiMapper;
use App\Shared\Api\Security\Attribute\Sensitive;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @phpstan-type ArrayHeaders array<string, string>
 * @phpstan-type ArrayContext array<mixed>
 */
class AbstractApiController extends AbstractController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ApiMapper $mapper,
    ) {
    }

    /**
     * @param ArrayHeaders $headers
     * @param ArrayContext $context
     */
    private function jsonResponse(
        SuccessResponse|ErrorResponse $data,
        int $status = 200,
        array $headers = [],
        array $context = [],
    ): JsonResponse {
        $queryParams = $this->requestStack->getCurrentRequest()->query->all();

        $ignore = $context[AbstractNormalizer::IGNORED_ATTRIBUTES] ?? [];
        $ignore = array_merge($ignore, explode(',', $queryParams['ignore'] ?? ''));
        $ignore = array_filter($ignore);

        $only = $context[AbstractNormalizer::ATTRIBUTES] ?? [];
        $only = array_merge($only, explode(',', $queryParams['only'] ?? ''));
        $only = array_filter($only);

        return $this->json(data: $data, status: $status, headers: $headers, context: array_merge($context, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => $ignore,
            AbstractNormalizer::ATTRIBUTES => [...$only, 'data', 'meta', 'success', 'message', 'code', 'errors'],
        ]));
    }

    /**
     * @param object|array<mixed>|bool|null $data
     * @param class-string                  $target
     * @param ArrayHeaders                  $headers
     * @param ArrayContext                  $context
     */
    public function successResponse(
        object|array|bool|null $data,
        string $target,
        ?ApiMetaInterface $meta = null,
        int $status = 200,
        array $headers = [],
        array $context = [],
    ): JsonResponse {
        if ($status < 200 || $status > 299) {
            throw new \LogicException('Status code must be between 200 and 299');
        }

        if (\is_array($data)) {
            foreach ($data as &$value) {
                $value = $this->mapper->map(source: $value, target: $target, context: [
                    'groups' => 'default',
                ]);
            }

            unset($value);
        } elseif (\is_object($data)) {
            $data = $this->mapper->map(source: $data, target: $target, context: [
                'groups' => 'default',
            ]);
        }

        if (\is_object($data)) {
            /** @var array<string> $ignoredAttributes */
            $ignoredAttributes = $context[AbstractNormalizer::IGNORED_ATTRIBUTES] ?? [];
            $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = [
                ...$ignoredAttributes,
                ...$this->resolveIgnoredAttributes($data),
            ];
        }

        dd($this->json($data, context: [
            'groups' => 'default',
        ])->getContent());

        return $this->jsonResponse(
            data: SuccessResponse::new(data: $data, meta: $meta, success: true),
            status: $status,
            headers: $headers,
            context: $context,
        );
    }

    /**
     * @return list<string>
     */
    private function resolveIgnoredAttributes(object $data): array
    {
        // Let ROLE_ADMIN see everything
        if ($this->isGranted('ROLE_ADMIN')) {
            return [];
        }

        /** @var list<string> $ignoredAttributes */
        $ignoredAttributes = [];

        foreach ((new \ReflectionClass($data))->getProperties() as $reflectionProperty) {
            $propertyName = $reflectionProperty->getName();

            $sensitiveAttribute = $this->getSensitiveAttribute($reflectionProperty);
            if ($sensitiveAttribute instanceof Sensitive && ! $this->isGranted($sensitiveAttribute->roles)) {
                $ignoredAttributes[] = $propertyName;
            }
        }

        return $ignoredAttributes;
    }

    private function getSensitiveAttribute(\ReflectionProperty|\ReflectionMethod $reflection): ?Sensitive
    {
        $attribute = $reflection->getAttributes(Sensitive::class)[0] ?? null;

        return $attribute?->newInstance();
    }

    /**
     * @param ArrayHeaders $headers
     * @param ArrayContext $context
     */
    public function booleanResponse(
        bool $data,
        ?ApiMetaInterface $meta = null,
        int $status = 200,
        array $headers = [],
        array $context = [],
    ): JsonResponse {
        if ($status < 200 || $status > 299) {
            throw new \LogicException('Status code must be between 200 and 299');
        }

        return $this->successResponse(data: $data, meta: $meta, status: $status, headers: $headers, context: $context);
    }

    /**
     * @param array<mixed> $errors
     * @param ArrayHeaders $headers
     * @param ArrayContext $context
     */
    public function errorResponse(
        array $errors = [],
        int $status = 400,
        array $headers = [],
        array $context = [],
    ): JsonResponse {
        if ($status < 400) {
            throw new \LogicException('Status code must be 400 or greater');
        }

        return $this->jsonResponse(
            data: ErrorResponse::create(message: 'An error occurred', code: $status, errors: $errors),
            status: $status,
            headers: $headers,
            context: $context,
        );
    }

    /**
     * @param ArrayHeaders $headers
     * @param ArrayContext $context
     */
    public function notFoundResponse(array $headers = [], array $context = []): JsonResponse
    {
        return $this->errorResponse(status: 404, headers: $headers, context: $context);
    }

    public function unauthorizedResponse(): JsonResponse
    {
        return $this->errorResponse(status: 401);
    }

    public function forbiddenResponse(): JsonResponse
    {
        return $this->errorResponse(status: 403);
    }

    public function noContentResponse(): JsonResponse
    {
        return $this->json(data: null, status: 204);
    }
}
