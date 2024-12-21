<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\LowerThanOperator;
use OpenApi\Attributes\Parameter;

class LowerThanDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return LowerThanOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, LowerThanOperator::operator()),
            description: 'Lower Than operator',
            in: 'query',
        );
    }
}
