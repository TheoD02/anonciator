<?php

declare(strict_types=1);

namespace App\Shared\Api;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class MapRelation
{
    public function __construct(
        public string $toProperty,
        public bool $many = false,
        public bool $allowEmpty = false,
    ) {
        if ($this->many === false && $this->allowEmpty) {
            throw new \InvalidArgumentException('allowEmpty can be set to true only for many relations');
        }
    }
}
