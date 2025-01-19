<?php

declare(strict_types=1);

namespace App\Shared\Api;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class RelationResolver
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    /**
     * @template TSource of object
     * @template TTarget of object
     *
     * @param TSource $source
     * @param TTarget $target
     *
     * @return TTarget
     */
    public function resolve(object $source, object $target): object
    {
        /** @var \ReflectionClass<TSource> $reflection */
        $reflection = new \ReflectionClass($source);
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->getPropertiesWithRelationObject($reflection) as $property) {
            if (!$property->isInitialized($source)) {
                continue;
            }

            /** @var ?Relation $relation */
            $relation = $property->getValue($source);
            if ($relation === null) {
                continue;
            }

            $mapRelation = $this->getMapRelation($property, $source);
            $targetProperty = $mapRelation->toProperty;
            $targetPropertyReflection = new \ReflectionProperty($target, $targetProperty);

            $collection = $this->getCollectionInstance($targetPropertyReflection, $target);

            if ($relation->set === []) {
                $this->handleEmptySet($collection, $mapRelation, $targetPropertyReflection, $target, $targetProperty);
                continue;
            }

            $targetType = $this->em->getClassMetadata($target::class)
                ->getAssociationTargetClass($targetProperty);

            $this->validateRelation($relation, $mapRelation, $property, $source, $collection);

            if ($relation->set) {
                $this->handleSetOperation(
                    $relation,
                    $collection,
                    $targetType,
                    $target,
                    $targetProperty,
                    $property,
                    $accessor
                );
                continue;
            }

            $this->handleAddRemoveOperations($relation, $collection, $targetType);
        }

        return $target;
    }

    private function getPropertiesWithRelationObject(\ReflectionClass $reflection): iterable
    {
        foreach ($reflection->getProperties() as $property) {
            $type = $property->getType();
            if (!$type instanceof \ReflectionNamedType) {
                continue;
            }

            if (in_array($type->getName(), [OneRelation::class, Relation::class], true)) {
                yield $property;
            }
        }
    }

    private function getMapRelation(\ReflectionProperty $property, object $source): MapRelation
    {
        $mapRelation = $property->getAttributes(MapRelation::class)[0] ?? null;
        if ($mapRelation === null) {
            throw new \RuntimeException(sprintf(
                'Property "%s::%s" is not annotated with MapRelation attribute',
                $source::class,
                $property->getName()
            ));
        }

        return $mapRelation->newInstance();
    }

    private function getCollectionInstance(
        \ReflectionProperty $targetPropertyReflection,
        object              $target
    ): ?Collection
    {
        $targetPropertyType = $targetPropertyReflection->getType();
        if (!$targetPropertyType instanceof \ReflectionNamedType) {
            throw new \RuntimeException('Property type must be a named type');
        }

        $isCollection = $targetPropertyType->getName() === Collection::class;
        $collection = $isCollection ? $targetPropertyReflection->getValue($target) : null;

        if ($isCollection && !$collection instanceof Collection) {
            throw new \RuntimeException('Collection property must be instance of Collection');
        }

        return $collection;
    }

    private function handleEmptySet(
        ?Collection         $collection,
        MapRelation         $mapRelation,
        \ReflectionProperty $targetPropertyReflection,
        object              $target,
        string              $targetProperty
    ): void
    {
        $collection?->clear();

        if ($mapRelation->allowEmpty === true) {
            return;
        }

        if ($targetPropertyReflection->getType()?->allowsNull() === false) {
            $this->throwValidationError(
                'Property is not nullable and no value was set',
                $target,
                $targetProperty
            );
        }
    }

    private function throwValidationError(
        string $message,
        object $target,
        string $property,
        array  $parameters = []
    ): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                $message,
                null,
                $parameters,
                $target,
                $property,
                null
            ),
        ]);

        throw HttpException::fromStatusCode(
            422,
            implode("\n", array_map(
                static fn($e): string|\Stringable => $e->getMessage(),
                iterator_to_array($violations)
            )),
            new ValidationFailedException($target, $violations)
        );
    }

    private function validateRelation(
        Relation            $relation,
        MapRelation         $mapRelation,
        \ReflectionProperty $property,
        object              $source,
        ?Collection         $collection
    ): void
    {
        if (!$mapRelation->many && count($relation->set ?? []) > 1) {
            throw new \RuntimeException(
                "Cannot set multiple values for non-collection property {$property->getName()}"
            );
        }

        if ($mapRelation->many && $collection === null) {
            throw new \RuntimeException(
                "Collection property {$property->getName()} must be initialized"
            );
        }
    }

    private function handleSetOperation(
        Relation            $relation,
        ?Collection         $collection,
        string              $targetType,
        object              $target,
        string              $targetProperty,
        \ReflectionProperty $property,
                            $accessor
    ): void
    {
        $collection?->clear();
        $this->validateEntityIds($relation->set, $targetType, $target, $targetProperty);

        foreach ($relation->set as $id) {
            $reference = $this->em->getReference($targetType, $id);

            if ($collection === null) {
                $accessor->setValue($target, $property->getName(), $reference);
            } else {
                $collection->add($reference);
            }
        }
    }

    private function validateEntityIds(
        array  $ids,
        string $targetType,
        object $target,
        string $targetProperty
    ): void
    {
        $databaseIds = $this->em->getRepository($targetType)
            ->createQueryBuilder('t')
            ->select('t.id')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getScalarResult();

        $databaseIds = array_column($databaseIds, 'id');
        if (count($databaseIds) !== count($ids)) {
            $missingIds = array_diff($ids, $databaseIds);
            $this->throwValidationError(
                'Some ids do not exist',
                $target,
                $targetProperty,
                ['ids' => $missingIds]
            );
        }
    }

    private function handleAddRemoveOperations(
        Relation    $relation,
        ?Collection $collection,
        string      $targetType
    ): void
    {
        $existingIds = $collection
            ?->map(fn(object $object): int|string => $this->getDoctrineId($object))
            ->toArray();

        $idsToAdd = array_diff($relation->add ?? [], $existingIds);
        $idsToRemove = array_intersect($relation->remove ?? [], $existingIds);

        foreach ($idsToAdd as $id) {
            $collection?->add($this->em->getReference($targetType, $id));
        }

        foreach ($idsToRemove as $id) {
            $collection?->removeElement($this->em->getReference($targetType, $id));
        }
    }

    private function getDoctrineId(object $object): int|string
    {
        $id = $this->em->getClassMetadata($object::class)->getIdentifierValues($object);
        $idValue = $id[0] ?? $id['id'];

        if (!is_string($idValue) && !is_int($idValue)) {
            throw new \RuntimeException('Doctrine id must be a string or integer value');
        }

        return $idValue;
    }
}
