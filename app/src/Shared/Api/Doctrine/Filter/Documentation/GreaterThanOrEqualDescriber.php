<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\GreaterThanOrEqualOperator;
use OpenApi\Attributes\Parameter;

class GreaterThanOrEqualDescriber implements OperatorDescriber
{
    private static ?Parameter $parameter = null;

    public function parameter(FilterDefinition $definition): Parameter
    {
        return self::$parameter ??= new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, GreaterThanOrEqualOperator::operator()),
            description: 'Greater than or equal operator',
            in: 'query',
        );
    }

    public static function operator(): string
    {
        return GreaterThanOrEqualOperator::class;
    }
}
