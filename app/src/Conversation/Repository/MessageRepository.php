<?php

declare(strict_types=1);

namespace App\Conversation\Repository;

use App\Conversation\Entity\Message;
use App\Shared\Doctrine\Repository\AbstractEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends AbstractEntityRepository<Message>
 */
class MessageRepository extends AbstractEntityRepository
{
    public function getEntityFqcn(): string
    {
        return Message::class;
    }

    #[\Override]
    protected function createPaginationQueryBuilder(): QueryBuilder
    {
        $qb = parent::createPaginationQueryBuilder();

        $qb->orderBy('e.createdAt', 'DESC');

        return $qb;
    }
}
