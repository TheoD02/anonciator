<?php

declare(strict_types=1);

namespace App\Shared\Api\Dto\Meta;

use App\Shared\Api\Doctrine\Pagination\Paginator;
use App\Shared\Api\Dto\Adapter\ApiMetaInterface;

readonly class PaginationMeta implements ApiMetaInterface
{
    public int $totalItems;

    public int $currentPage;

    public int $lastPage;

    public int $firstPage;

    public int $maxPerPage;

    public static function fromDoctrinePaginator(Paginator $paginator): self
    {
        $total = $paginator->count();

        $meta = new self();

        $meta->totalItems = $total;
        $meta->currentPage = $paginator->getPaginationFilterQuery()->page;
        $meta->lastPage = (int) ceil($total / $paginator->getPaginationFilterQuery()->limit);
        $meta->firstPage = 1;
        $meta->maxPerPage = 100;

        return $meta;
    }
}
