<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\EndWithOperator;
use OpenApi\Attributes\Parameter;

class EndWithDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return EndWithOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, EndWithOperator::operator()),
            description: 'End with operator',
            in: 'query',
        );
    }
}
