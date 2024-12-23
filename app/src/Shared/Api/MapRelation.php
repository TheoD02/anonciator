<?php

namespace App\Shared\Api;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class MapRelation
{
    public function __construct(
        public string $toProperty,
        public bool $many = false,
    )
    {
    }
}
