<?php

declare(strict_types=1);

namespace App\Conversation\Dto\Filter;

use App\Shared\Api\Doctrine\Filter\Adapter\ORMQueryBuilderFilterQueryAwareInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Serializer\Attribute\Ignore;

class PaginateMessageFilter implements ORMQueryBuilderFilterQueryAwareInterface
{
    #[Ignore]
    public int $announceId;

    public function applyToORMQueryBuilder(QueryBuilder $qb): void
    {
        if (isset($this->announceId)) {
            $qb->leftJoin('e.announce', 'a');
            $qb->andWhere('a.id = :announceId')
                ->setParameter('announceId', $this->announceId)
            ;
        }
    }
}
