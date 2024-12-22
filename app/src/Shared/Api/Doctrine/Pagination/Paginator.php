<?php

namespace App\Shared\Api\Doctrine\Pagination;

use App\Shared\Api\PaginationFilterQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class Paginator extends \Doctrine\ORM\Tools\Pagination\Paginator
{
    public function __construct(Query|QueryBuilder $query, bool $fetchJoinCollection = true, private PaginationFilterQuery $paginationFilterQuery = new PaginationFilterQuery())
    {
        parent::__construct($query, $fetchJoinCollection);
    }

    public function getPaginationFilterQuery(): PaginationFilterQuery
    {
        return $this->paginationFilterQuery;
    }


}
