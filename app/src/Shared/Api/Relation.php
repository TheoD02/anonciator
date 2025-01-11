<?php

declare(strict_types=1);

namespace App\Shared\Api;

class Relation
{
    public function __construct(
        public array $set = [],
        public array $add = [],
        public array $remove = [],
    ) {
    }
}
