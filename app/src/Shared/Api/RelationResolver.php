<?php

namespace App\Shared\Api;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class RelationResolver
{
    public function __construct(
        private readonly EntityManagerInterface $em
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

        $properties = $this->getPropertiesWithRelationObject($reflection);
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($properties as $property) {
            if ($property->isInitialized($source) === false) {
                continue;
            }
            
            /** @var ?Relation $relation */
            $relation = $property->getValue($source);
            if ($relation === null) {
                continue;
            }

            $mapRelation = $property->getAttributes(MapRelation::class)[0] ?? null;

            if ($mapRelation === null) {
                throw new \RuntimeException(\sprintf(
                    'Property "%s::%s" is not annotated with MapRelation',
                    $source::class,
                    $property->getName()
                ));
            }

            /** @var MapRelation $mapRelationInstance */
            $mapRelationInstance = $mapRelation->newInstance();

            $targetProperty = $mapRelationInstance->toProperty;
            $many = $mapRelationInstance->many;

            $targetPropertyReflection = new \ReflectionProperty($target, $targetProperty);
            $targetPropertyType = $targetPropertyReflection->getType();
            if ($targetPropertyType instanceof \ReflectionNamedType === false) {
                throw new \RuntimeException(\sprintf(
                    'Property "%s::%s" is not a named type. Type "%s" is not supported yet.',
                    $source::class,
                    $targetProperty,
                    $targetPropertyType !== null ? $targetPropertyType::class : 'null',
                ));
            }

            $isCollection = $targetPropertyType->getName() === Collection::class;
            /** @var object|null $collectionInstance */
            $collectionInstance = $isCollection ? $targetPropertyReflection->getValue($target) : null;

            if ($isCollection && !$collectionInstance instanceof Collection) {
                throw new \RuntimeException(\sprintf(
                    'Property "%s::%s" is a collection but the collection instance is not a collection. Type "%s" is not supported yet.',
                    $source::class,
                    $targetProperty,
                    $collectionInstance !== null ? $collectionInstance::class : 'null',
                ));
            }

            /** @var ?Collection<array-key, object> $collectionInstance */

            if ($relation->set === []) {
                $collectionInstance?->clear();

                return $target;
            }

            $targetType = $this->em->getClassMetadata($target::class)->getAssociationTargetClass($targetProperty);

            if ($many === false && \count($relation->set ?? []) > 1) {
                // Ensure that we are not trying to set more than one value to a non-collection property
                throw new \RuntimeException(\sprintf(
                    'Not possible to define more than one values in "set" field for property "%s::%s". Please input only one id or if is a collection, change the "many" attribute to true.',
                    $source::class,
                    $property->getName(),
                ),);
            }

            if ($many === true && $collectionInstance === null) {
                throw new \RuntimeException(\sprintf(
                    'Attribute "many" is true but can\'t resolve collection instance for property "%s::%s". Please check that property is typed with %s.',
                    $source::class,
                    $property->getName(),
                    Collection::class,
                ),);
            }

            if ($relation->set) {
                $collectionInstance?->clear();

                foreach ($relation->set as $id) {
                    $reference = $this->em->getReference($targetType, $id);
                    if ($reference === null) {
                        throw new \RuntimeException(\sprintf('%s with id "%s" does not exist', $targetType, $id));
                    }

                    if ($collectionInstance === null) {
                        $accessor->setValue($target, $property->getName(), $reference);
                        continue;
                    }

                    $collectionInstance->add($reference);
                }

                continue;
            }

            if ($collectionInstance === null || $many === false) {
                // Ensure that we are not trying to add or remove values to a non-collection property
                throw new \RuntimeException(
                    'Not possible to "add" or "remove" values to non-collection property. Please use "set" operation.',
                );
            }

            $duplicateIds = array_intersect($relation->add ?? [], $relation->remove ?? []);
            if ($duplicateIds !== []) {
                // Ensure that we are not trying to add or remove duplicate values (because we don't know which has priority)
                throw new \RuntimeException(\sprintf(
                    'Duplicate ids "%s" are not allowed',
                    implode(', ', $duplicateIds)
                ));
            }

            // We have all information to set the values in case of "add" and "remove"
            $existingIds = $collectionInstance
                ->map(fn(object $object): int|string => $this->getDoctrineId($object))
                ->toArray();
            $idsToAdd = array_diff($relation->add ?? [], $existingIds);
            $idsToRemove = array_intersect($relation->remove ?? [], $existingIds);

            foreach ($idsToAdd as $idToAdd) {
                $reference = $this->em->getReference($targetType, $idToAdd);
                if ($reference === null) {
                    throw new \RuntimeException(\sprintf('%s with id "%s" does not exist', $targetType, $idToAdd));
                }

                $collectionInstance->add($reference);
            }

            foreach ($idsToRemove as $idToRemove) {
                $reference = $this->em->getReference($targetType, $idToRemove);
                if ($reference === null) {
                    throw new \RuntimeException(\sprintf('%s with id "%s" does not exist', $targetType, $idToRemove));
                }

                $collectionInstance->removeElement($reference);
            }
        }

        return $target;
    }

    /**
     * @template T of object
     *
     * @param \ReflectionClass<T> $reflection
     *
     * @return iterable<\ReflectionProperty>
     */
    private function getPropertiesWithRelationObject(\ReflectionClass $reflection): iterable
    {
        foreach ($reflection->getProperties() as $reflectionProperty) {
            $type = $reflectionProperty->getType();
            if ($type === null) {
                continue;
            }

            if ($type instanceof \ReflectionNamedType === false) {
                continue;
            }

            if ($type->getName() === OneRelation::class || $type->getName() === Relation::class) {
                yield $reflectionProperty;
            }
        }
    }

    private function getDoctrineId(object $object): int|string
    {
        $id = $this->em->getClassMetadata($object::class)->getIdentifierValues($object);

        $idValue = $id[0] ?? $id['id'];
        if (!\is_string($idValue) && !\is_int($idValue)) {
            throw new \RuntimeException(
                'Doctrine id is not a string or a int value. Doctrine id must be a string or a int value.'
            );
        }

        return $idValue;
    }
}
