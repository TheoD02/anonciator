<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\LowerThanOrEqualOperator;
use OpenApi\Attributes\Parameter;

class LowerThanOrEqualDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return LowerThanOrEqualOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, LowerThanOrEqualOperator::operator()),
            description: 'Lower Than Or Equal operator',
            in: 'query',
        );
    }
}
