<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\InOperator;
use OpenApi\Attributes\Parameter;

class InOperatorDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return InOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, InOperator::operator()),
            description: 'In operator',
            in: 'query',
            allowEmptyValue: false,
        );
    }
}
