<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\IsNullOperator;
use OpenApi\Attributes\Parameter;

class IsNullDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return IsNullOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, IsNullOperator::operator()),
            description: 'Is Null operator',
            in: 'query',
        );
    }
}
