<?php

declare(strict_types=1);

namespace App\Shared\Api\Mapper;

use AutoMapper\AutoMapper;
use AutoMapper\MapperContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @phpstan-import-type MapperContextArray from MapperContext
 */
class ApiMapper
{
    private readonly ?Request $request;

    public function __construct(
        private readonly AutoMapper $autoMapper,
        private readonly RequestStack $requestStack,
        //        private readonly RelationResolver $relationResolver,
    ) {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    /**
     * @template Source of object
     * @template Target of object
     *
     * @param Source|array<mixed>                      $source
     * @param class-string<Target>|array<mixed>|Target $target
     *
     * @return ($target is class-string|Target ? Target|null : array<mixed>|null)
     */
    public function map(array|object $source, string|array|object $target, array $context = []): array|object|null
    {
        $queryParams = $this->request->query->all();
        $include = $queryParams['include'] ?? '';
        $ignore = $queryParams['ignore'] ?? '';
        $only = $queryParams['only'] ?? '';

        foreach ([&$include, &$ignore, &$only] as &$value) {
            $value = \is_array($value) ? array_filter($value) : array_filter(explode(',', (string) $value));
        }

        unset($value);

        $context = array_merge($context, [
            'include' => $include,
            MapperContext::IGNORED_ATTRIBUTES => $ignore,
            MapperContext::ALLOWED_ATTRIBUTES => $only,
        ]);

        $target = $this->autoMapper->map($source, $target, $context);
        if ($target !== null) {
            //        $this->relationResolver->resolve($source, $target);
        }

        return $target;
    }

    /**
     * @template Source of object
     * @template Target of object
     *
     * @param Source|array<mixed>                      $source
     * @param class-string<Target>|array<mixed>|Target $target
     *
     * @return ($target is class-string|Target ? Target|null : array<mixed>|null)
     */
    public function patch(array|object $source, string|array|object $target, array $context = []): array|object|null
    {
        if (\is_object($source) === false) {
            return $this->map($source, $target, $context);
        }

        $reflectionClass = new \ReflectionClass($source);
        $data = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isInitialized($source) === false) {
                continue;
            }

            if (
                $reflectionProperty->getValue($source) === null
                //                && $reflectionProperty->getType()?->getName() === Relation::class
            ) {
                continue;
            }

            $data[$reflectionProperty->getName()] = $reflectionProperty->getValue($source);
        }

        $target = $this->autoMapper->map($data, $target, $context);
        if ($target !== null) {
            //        $this->relationResolver->resolve($source, $target);
        }

        return $target;
    }
}
