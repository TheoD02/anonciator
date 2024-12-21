<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\NotContainOperator;
use OpenApi\Attributes\Parameter;

class NotContainDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return NotContainOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, NotContainOperator::operator()),
            description: 'Not Contain operator',
            in: 'query',
        );
    }
}
