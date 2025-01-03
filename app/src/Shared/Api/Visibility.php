<?php

declare(strict_types=1);

namespace App\Shared\Api;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Visibility
{
    public function __construct(
        public bool $external = true,
        public bool $internal = true,
    ) {
    }
}
