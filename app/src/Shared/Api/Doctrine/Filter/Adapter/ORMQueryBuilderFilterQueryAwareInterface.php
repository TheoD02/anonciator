<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Adapter;

use App\Shared\FilterQueryInterface;
use Doctrine\ORM\QueryBuilder;

interface ORMQueryBuilderFilterQueryAwareInterface extends FilterQueryInterface
{
    public function applyToORMQueryBuilder(QueryBuilder $qb): void;
}
