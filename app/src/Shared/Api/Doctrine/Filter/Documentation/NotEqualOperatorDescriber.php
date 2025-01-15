<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\NotEqualOperator;
use OpenApi\Attributes\Parameter;

class NotEqualOperatorDescriber implements OperatorDescriber
{
    private static ?Parameter $parameter = null;

    public static function operator(): string
    {
        return NotEqualOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return self::$parameter ??= new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, NotEqualOperator::operator()),
            description: 'Not equal operator',
            in: 'query',
        );
    }
}
