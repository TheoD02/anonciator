<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\IsEmptyOperator;
use OpenApi\Attributes\Parameter;

class IsEmptyDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return IsEmptyOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, IsEmptyOperator::operator()),
            description: 'Is Empty operator',
            in: 'query',
        );
    }
}
