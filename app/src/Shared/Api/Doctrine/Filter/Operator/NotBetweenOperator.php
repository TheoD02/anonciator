<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Operator;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Trait\QueryBuilderParameterTrait;
use Doctrine\ORM\QueryBuilder;

class NotBetweenOperator implements OperatorInterface
{
    use QueryBuilderParameterTrait;

    public static function operator(): string
    {
        return 'nbtw';
    }

    public function apply(QueryBuilder $qb, FilterDefinition $definition, string|array $value): void
    {
        [$value1, $value2] = explode(',', $value);

        $parameterName1 = $this->generateRandomParameterName();
        $parameterName2 = $this->generateRandomParameterName();

        $qb->setParameter($parameterName1, $value1);
        $qb->setParameter($parameterName2, $value2);

        $alias = $this->getAlias($qb, $definition);

        $qb->andWhere("{$alias}.{$definition->field} NOT BETWEEN :{$parameterName1} AND :{$parameterName2}");
    }
}
