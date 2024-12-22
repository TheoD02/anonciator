<?php

namespace App\Announce\Dto\Filter;

use Doctrine\ORM\QueryBuilder;

interface IncludeRelationFilterQuery
{
    public array $includes {
        get;
        set;
    }

    public function applyIncludes(QueryBuilder $qb): void;
}
