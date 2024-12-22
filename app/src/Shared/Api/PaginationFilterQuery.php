<?php

declare(strict_types=1);

namespace App\Shared\Api;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationFilterQuery
{
    public function __construct(
        #[Assert\Positive()]
        public int $page = 1,
        #[Assert\Positive()]
        #[Assert\LessThanOrEqual(100_000)]
        public int $limit = 10,
    ) {
    }
}
