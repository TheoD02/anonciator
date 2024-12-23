<?php

declare(strict_types=1);

namespace App\Announce\Service;

use App\Announce\Dto\Filter\AnnounceFilterQuery;
use App\Announce\Dto\Payload\CreateAnnouncePayload;
use App\Announce\Dto\Payload\PartialUpdateAnnouncePayload;
use App\Announce\Dto\Payload\UpdateAnnouncePayload;
use App\Announce\Entity\Announce;
use App\Announce\Event\AnnounceCreatedEvent;
use App\Announce\Repository\AnnounceRepository;
use App\Shared\Api\Doctrine\Pagination\Paginator;
use App\Shared\Api\PaginationFilterQuery;
use App\Shared\Api\RelationResolver;
use AutoMapper\AutoMapperInterface;
use AutoMapper\MapperContext;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AnnounceService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly AnnounceRepository $repository,
        private readonly AutoMapperInterface $mapper,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher,
        private RelationResolver $relationResolver,
    )
    {
    }

    public function createAnnounceFromPayload(CreateAnnouncePayload $payload): Announce
    {
        $announce = $this->mapper->map($payload, Announce::class);

        $this->relationResolver->resolve($payload, $announce);

        return $this->createAnnounce($announce);
    }

    public function createAnnounce(Announce $announce, bool $flush = true): Announce
    {
        $this->em->persist($announce);

        if ($flush) {
            $this->em->flush();
        }

        $this->logger?->info('Announce created', [
            'id' => $announce->getId(),
        ]);

        $this->dispatcher->dispatch(new AnnounceCreatedEvent($announce));

        return $announce;
    }

    public function paginate(AnnounceFilterQuery $query, PaginationFilterQuery $paginationFilterQuery): Paginator
    {
        return $this->repository->paginate($query, $paginationFilterQuery);
    }

    public function updateAnnounceFromPayload(int $id, UpdateAnnouncePayload $payload): Announce
    {
        $announce = $this->getAnnounceById($id);

        $this->mapper->map($payload, $announce);

        $this->relationResolver->resolve($payload, $announce);

        return $this->updateAnnounce($announce);
    }

    public function getAnnounceById(int $id): Announce
    {
        return $this->repository->find($id);
    }

    public function updateAnnounce(Announce $announce, bool $flush = true): Announce
    {
        if ($flush) {
            $this->em->flush();
        }

        $this->logger?->info('Announce updated', [
            'id' => $announce->getId(),
        ]);

        return $announce;
    }

    public function partialUpdateAnnounceFromPayload(int $id, PartialUpdateAnnouncePayload $payload): Announce
    {
        $announce = $this->getAnnounceById($id);

        $this->mapper->map($payload, $announce, [MapperContext::SKIP_UNINITIALIZED_VALUES => true]);

        $this->relationResolver->resolve($payload, $announce);

        return $this->updateAnnounce($announce);
    }
}
