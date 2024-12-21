<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\EqualOperator;
use OpenApi\Attributes\Parameter;

class EqualDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return EqualOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, EqualOperator::operator()),
            description: 'Equal operator',
            in: 'query',
        );
    }
}
