<?php

namespace App\Shared\Api;

use Stringable;

class OneRelation implements Stringable
{
    public int|string $set;

    public function __toString(): string
    {
        return (string)$this->set;
    }
}
