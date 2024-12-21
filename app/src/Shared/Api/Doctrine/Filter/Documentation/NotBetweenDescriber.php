<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\NotBetweenOperator;
use OpenApi\Attributes\Parameter;

class NotBetweenDescriber implements OperatorDescriber
{
    public static function operator(): string
    {
        return NotBetweenOperator::class;
    }

    public function parameter(FilterDefinition $definition): Parameter
    {
        return new Parameter(
            name: \sprintf('%s[%s]', $definition->publicName, NotBetweenOperator::operator()),
            description: 'Not Between operator',
            in: 'query',
        );
    }
}
