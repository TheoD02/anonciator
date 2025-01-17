<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\GreaterThanOperator;
use OpenApi\Attributes\Parameter;

class GreaterThanDescriber implements OperatorDescriber
{
    private static ?Parameter $parameter = null;

    public function parameter(FilterDefinition $definition): Parameter
    {
        return self::$parameter ??= new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, GreaterThanOperator::operator()),
            description: 'Greater than operator',
            in: 'query',
        );
    }

    public static function operator(): string
    {
        return GreaterThanOperator::class;
    }
}
