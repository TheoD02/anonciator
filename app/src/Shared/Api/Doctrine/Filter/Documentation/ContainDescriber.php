<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\ContainOperator;
use OpenApi\Attributes\Parameter;

class ContainDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return ContainOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, ContainOperator::operator()),
            description: 'Contain operator',
            in: 'query',
        );
    }
}
