<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Operator;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Trait\QueryBuilderParameterTrait;
use Doctrine\ORM\QueryBuilder;

class IsEmptyOperator implements OperatorInterface
{
    use QueryBuilderParameterTrait;

    public static function operator(): string
    {
        return 'empty';
    }

    public function apply(QueryBuilder $qb, FilterDefinition $definition, string|array $value): void
    {
        $shouldBeEmpty = $value === 'true' || $value === '1' || $value === '';

        $alias = $this->getAlias($qb, $definition);

        $conditions = [
            $shouldBeEmpty ? "{$alias}.{$definition->field} = ''" : "{$alias}.{$definition->field} != ''",
            $shouldBeEmpty ? "{$alias}.{$definition->field} = 0" : "{$alias}.{$definition->field} != 0",
        ];

        $qb->andWhere(implode(' OR ', $conditions));
    }
}
