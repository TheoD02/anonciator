<?php

namespace App\Announce\Dto\Payload;


use AutoMapper\Transformer\PropertyTransformer\PropertyTransformerInterface;
use function dd;

class RelationTransformer implements PropertyTransformerInterface
{
    public function transform(mixed $value, object|array $source, array $context): mixed
    {
        dd($value, $source, $context);
    }
}
