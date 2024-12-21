<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\BetweenOperator;
use OpenApi\Attributes\Parameter;

class BetweenDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return BetweenOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, BetweenOperator::operator()),
            description: 'Between operator',
            in: 'query',
        );
    }
}
