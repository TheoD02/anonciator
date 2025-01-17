<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Operator;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Trait\QueryBuilderParameterTrait;
use Doctrine\ORM\QueryBuilder;

class IsNullOperator implements OperatorInterface
{
    use QueryBuilderParameterTrait;

    public static function operator(): string
    {
        return 'isnull';
    }

    public function apply(QueryBuilder $qb, FilterDefinition $definition, string|array $value): void
    {
        $shouldBeNull = $value === 'true' || $value === '1' || $value === '';

        $alias = $this->getAlias($qb, $definition);

        $condition = $shouldBeNull ? 'IS NULL' : 'IS NOT NULL';
        $qb->andWhere("{$alias}.{$definition->field} {$condition}");
    }
}
