<?php

namespace App\Announce\Dto;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Visibility
{
    public function __construct(
        public bool $external = true,
        public bool $internal = true,
    )
    {
    }
}
