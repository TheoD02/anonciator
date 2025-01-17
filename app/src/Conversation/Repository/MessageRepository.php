<?php

declare(strict_types=1);

namespace App\Conversation\Repository;

use App\Conversation\Entity\Message;
use App\Shared\Api\PaginationFilterQuery;
use App\Shared\Doctrine\Repository\AbstractEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends AbstractEntityRepository<Message>
 */
class MessageRepository extends AbstractEntityRepository
{
    public function getEntityFqcn(): string
    {
        return Message::class;
    }

    public function getMessagesForConversation(int $id, PaginationFilterQuery $paginationFilterQuery): Paginator
    {
        $qb = parent::createPaginationQueryBuilder();

        $qb->andWhere('e.conversation = :conversationId')
            ->setParameter('conversationId', $id)
        ;

        return $this->paginator->paginate($qb, paginationFilterQuery: $paginationFilterQuery);
    }

    #[\Override]
    protected function createPaginationQueryBuilder(): QueryBuilder
    {
        $qb = parent::createPaginationQueryBuilder();

        $qb->orderBy('e.createdAt', 'DESC');

        return $qb;
    }
}
