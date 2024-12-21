<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\NotEndWithOperator;
use OpenApi\Attributes\Parameter;

class NotEndWithDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return NotEndWithOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, NotEndWithOperator::operator()),
            description: 'Not End with operator',
            in: 'query',
        );
    }
}
