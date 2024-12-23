<?php

declare(strict_types=1);

namespace App\Shared\Api;

use App\Announce\Dto\Visibility;
use App\Shared\Api\Doctrine\Pagination\Paginator;
use App\Shared\Api\Dto\Adapter\ApiMetaInterface;
use App\Shared\Api\Dto\Meta\PaginationMeta;
use App\Shared\Api\Dto\Response\ErrorResponse;
use App\Shared\Api\Dto\Response\SuccessResponse;
use App\Shared\Api\Security\Attribute\Sensitive;
use AutoMapper\AutoMapperInterface as JoliCodeAutoMapperInterface;
use AutoMapperPlus\AutoMapperInterface;
use Rekalogika\Mapper\IterableMapperInterface;
use Rekalogika\Mapper\MapperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @phpstan-type ArrayHeaders array<string, string>
 * @phpstan-type ArrayContext array<mixed>
 */
class AbstractApiController extends AbstractController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly JoliCodeAutoMapperInterface $joliCodeAutoMapper,
        private readonly NormalizerInterface $normalizer,
        protected readonly Stopwatch $sw,
    )
    {
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
    ): JsonResponse
    {
        if ($status < 200 || $status > 299) {
            throw new \LogicException('Status code must be between 200 and 299');
        }

        return $this->successResponse(data: $data, meta: $meta, status: $status, headers: $headers, context: $context);
    }

    /**
     * @param object|array<mixed>|bool|null $data
     * @param class-string $target
     * @param ArrayHeaders $headers
     * @param ArrayContext $context
     */
    public function successResponse(
        object|array|bool|null $data,
        string $target,
        array $groups = [],
        ?ApiMetaInterface $meta = null,
        int $status = 200,
        array $headers = [],
        array $context = [],
    ): Response
    {
        if ($status < 200 || $status > 299) {
            throw new \LogicException('Status code must be between 200 and 299');
        }

        if ($data instanceof Paginator) {
            $meta = PaginationMeta::fromDoctrinePaginator($data);
        }

        $ctx = ['groups' => $groups];
        $this->sw->start('map_response');
        $data = \is_iterable($data) ? array_map(fn($item) => $this->joliCodeAutoMapper->map($item, $target, $ctx), $data->getIterator()->getArrayCopy()) : $this->joliCodeAutoMapper->map($data, $target, $ctx);
        $this->sw->stop('map_response');

        $context[AbstractObjectNormalizer::SKIP_UNINITIALIZED_VALUES] = $context[AbstractObjectNormalizer::SKIP_UNINITIALIZED_VALUES] ?? false;
        $context[AbstractNormalizer::GROUPS] = [...$groups, ...($context[AbstractNormalizer::GROUPS] ?? [])];

        $dataClass = \is_array($data) ? $data[0] : $data;
        if ($dataClass !== null && (\is_array($data) || \is_object($data))) {
            /** @var array<string> $ignoredAttributes */
            $ignoredAttributes = $context[AbstractNormalizer::IGNORED_ATTRIBUTES] ?? [];
            $ignoredAttributesFromRequest = $this->requestStack->getCurrentRequest()->query->get('ignore', '');
            $ignoredAttributesFromRequest = explode(',', $ignoredAttributesFromRequest);
            $ignoredAttributes = [...$ignoredAttributes, ...$ignoredAttributesFromRequest];
            $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = [
                ...$ignoredAttributes,
                ...$this->resolveIgnoredAttributes($dataClass),
            ];
        }

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
        $isExternal = false; // See how to resolve this in HTTP context when user logic implemented

        $reflectionClass = new \ReflectionClass($data);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $propertyName = $reflectionProperty->getName();

            $sensitiveAttribute = $this->getSensitiveAttribute($reflectionProperty);
            if ($sensitiveAttribute instanceof Sensitive && !$this->isGranted($sensitiveAttribute->roles)) {
                $ignoredAttributes[] = $propertyName;
            }

            $visibilityAttribute = $this->getVisibilityAttribute($reflectionProperty);
            if ($isExternal && $visibilityAttribute->external === false) {
                $ignoredAttributes[] = $propertyName;
            } elseif ($isExternal === false && $visibilityAttribute->internal === false) {
                $ignoredAttributes[] = $propertyName;
            }
        }

        return $ignoredAttributes;
    }

    private function getSensitiveAttribute(\ReflectionProperty|\ReflectionMethod $reflection): ?Sensitive
    {
        return ($reflection->getAttributes(Sensitive::class)[0] ?? null)?->newInstance();
    }

    private function getVisibilityAttribute(\ReflectionProperty $reflectionProperty): Visibility
    {
        $visibilityAttribute = $reflectionProperty->getAttributes(Visibility::class)[0] ?? null;

        return $visibilityAttribute?->newInstance() ?? new Visibility();
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
    ): JsonResponse
    {
        $queryParams = $this->requestStack->getCurrentRequest()->query->all();

        $ignore = $context[AbstractNormalizer::IGNORED_ATTRIBUTES] ?? [];
        $ignore = array_merge($ignore, explode(',', $queryParams['ignore'] ?? ''));
        $ignore = \array_filter($ignore);

        $only = $context[AbstractNormalizer::ATTRIBUTES] ?? [];
        $only = array_merge($only, explode(',', $queryParams['only'] ?? ''));
        $only = \array_filter($only);

        if ($data instanceof SuccessResponse) {
            $data->data = $this->normalizer->normalize($data->data, context: $context);
        }

        return $this->json(
            data: $data,
            status: $status,
            headers: $headers,
            context: array_merge($context, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => $ignore,
                AbstractNormalizer::ATTRIBUTES => [...$only, 'data', 'meta', 'success', 'message', 'code', 'errors'],
                AbstractNormalizer::GROUPS => [],
            ])
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
    ): JsonResponse
    {
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
