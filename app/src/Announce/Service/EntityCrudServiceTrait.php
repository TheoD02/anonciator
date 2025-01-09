<?php

declare(strict_types=1);

namespace App\Announce\Service;

use App\Shared\Api\Doctrine\Pagination\Paginator;
use App\Shared\Api\PaginationFilterQuery;
use App\Shared\Api\RelationResolver;
use App\Shared\Doctrine\Repository\AbstractEntityRepository;
use App\Shared\Exception\GenericDomainModelNotFoundException;
use App\Shared\FilterQueryInterface;
use App\Shared\PayloadInterface;
use AutoMapper\AutoMapperInterface;
use AutoMapper\MapperContext;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @template T
 */
trait EntityCrudServiceTrait
{
    protected AutoMapperInterface $mapper;

    protected EntityManagerInterface $em;

    protected EventDispatcherInterface $dispatcher;

    protected RelationResolver $relationResolver;

    protected ?LoggerInterface $logger = null;

    protected ?AbstractEntityRepository $repository = null;

    #[Required]
    public function setMapper(AutoMapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    #[Required]
    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    #[Required]
    public function setRelationResolver(RelationResolver $relationResolver): void
    {
        $this->relationResolver = $relationResolver;
    }

    #[Required]
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return T
     */
    public function createEntityFromPayload(PayloadInterface $payload): object
    {
        $entity = $this->mapper->map($payload, $this->getEntityClass());

        $this->relationResolver->resolve($payload, $entity);

        return $this->createEntity($entity);
    }

    /**
     * @return class-string<T>
     */
    abstract protected function getEntityClass(): string;

    /**
     * @param T $entity
     *
     * @return T
     */
    public function createEntity(object $entity, bool $flush = true): object
    {
        $this->em->persist($entity);

        if ($flush) {
            $this->em->flush();
        }

        $this->logger?->info('Entity created', [
            'entity' => $entity::class,
            'id' => $entity->getId(),
        ]);

        // $this->dispatcher->dispatch(new $this->getEntityCreatedEventClass($entity));

        return $entity;
    }

    /**
     * @return T
     */
    public function updateEntityFromPayload(int $id, PayloadInterface $payload): object
    {
        /** @var T $entity */
        $entity = $this->getEntityById($id, fail: true);

        $this->mapper->map($payload, $entity);

        $this->relationResolver->resolve($payload, $entity);

        return $this->updateEntity($entity);
    }

    /**
     * @return ($fail is true ? T : T|null)
     */
    public function getEntityById(int $id, bool $fail = false): ?object
    {
        /** @var T|null $entity */
        $entity = $this->getRepository()->find($id);

        if ($entity === null && $fail) {
            throw GenericDomainModelNotFoundException::withId($id, $this->getEntityClass());
        }

        return $entity;
    }

    public function getRepository(): AbstractEntityRepository
    {
        if ($this->repository === null) {
            /** @var AbstractEntityRepository<T> $repository */
            $repository = $this->em->getRepository($this->getEntityClass());

            $this->repository = $repository;
        }

        return $this->repository;
    }

    /**
     * @param T $entity
     *
     * @return T
     */
    public function updateEntity(object $entity, bool $flush = true): object
    {
        $this->em->persist($entity);

        if ($flush) {
            $this->em->flush();
        }

        $this->logger?->info('Entity updated', [
            'entity' => $entity::class,
            'id' => $entity->getId(),
        ]);

        // $this->dispatcher->dispatch(new $this->getEntityUpdatedEventClass($entity));

        return $entity;
    }

    public function partialUpdateEntityFromPayload(int $id, PayloadInterface $payload): object
    {
        /** @var T $entity */
        $entity = $this->getEntityById($id, fail: true);

        $this->mapper->map($payload, $entity, [
            MapperContext::SKIP_UNINITIALIZED_VALUES => true,
        ]);

        $this->relationResolver->resolve($payload, $entity);

        return $this->updateEntity($entity);
    }

    public function deleteEntityFromId(int $id): void
    {
        /** @var T $entity */
        $entity = $this->getEntityById($id, fail: true);

        $this->deleteEntity($entity);
    }

    /**
     * @param T $entity
     */
    public function deleteEntity(object $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();

        $this->logger?->info('Entity deleted', [
            'entity' => $entity::class,
            'id' => $entity->getId(),
        ]);
    }

    /**
     * @return Paginator<T>
     */
    public function paginateEntities(
        ?FilterQueryInterface $filterQuery = null,
        ?PaginationFilterQuery $paginationFilterQuery = null,
    ): object {
        return $this->getRepository()->paginate(
            $filterQuery,
            $paginationFilterQuery ?? new PaginationFilterQuery()
        );
    }
}
