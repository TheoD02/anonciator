<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Pagination;

use App\Shared\Api\PaginationFilterQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class Paginator extends \Doctrine\ORM\Tools\Pagination\Paginator
{
    public function __construct(
        Query|QueryBuilder $query,
        bool $fetchJoinCollection = true,
        private readonly PaginationFilterQuery $paginationFilterQuery = new PaginationFilterQuery(),
    ) {
        parent::__construct($query, $fetchJoinCollection);
    }

    public function getPaginationFilterQuery(): PaginationFilterQuery
    {
        return $this->paginationFilterQuery;
    }
}
