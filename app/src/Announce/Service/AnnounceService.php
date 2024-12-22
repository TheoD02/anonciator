<?php

namespace App\Announce\Service;

use App\Announce\Dto\Filter\AnnounceFilterQuery;
use App\Announce\Dto\Payload\CreateAnnouncePayload;
use App\Announce\Entity\Announce;
use App\Announce\Event\AnnounceCreatedEvent;
use App\Announce\Repository\AnnounceRepository;
use App\Shared\Api\Doctrine\Pagination\Paginator;
use App\Shared\Api\PaginationFilterQuery;
use AutoMapperPlus\AutoMapperInterface;
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
    )
    {
    }

    public function createAnnounceFromPayload(CreateAnnouncePayload $payload): Announce
    {
        $announce = $this->mapper->map($payload, Announce::class);

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
}
