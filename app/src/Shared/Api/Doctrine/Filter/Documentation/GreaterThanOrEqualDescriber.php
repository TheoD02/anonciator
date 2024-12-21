<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\GreaterThanOrEqualOperator;
use OpenApi\Attributes\Parameter;

class GreaterThanOrEqualDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return GreaterThanOrEqualOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, GreaterThanOrEqualOperator::operator()),
            description: 'Greater Than Or Equal operator',
            in: 'query',
        );
    }
}
