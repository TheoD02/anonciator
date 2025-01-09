<?php

declare(strict_types=1);

namespace App\Announce\Dto\Filter;

use Doctrine\ORM\QueryBuilder;

interface IncludeRelationFilterQuery
{
    public array $include {
        get;
    }

    public function applyIncludes(QueryBuilder $qb): void;
}
