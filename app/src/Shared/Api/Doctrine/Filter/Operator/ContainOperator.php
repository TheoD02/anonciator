<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Operator;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Trait\QueryBuilderParameterTrait;
use Doctrine\ORM\QueryBuilder;

class ContainOperator implements OperatorInterface
{
    use QueryBuilderParameterTrait;

    public static function operator(): string
    {
        return 'ctn';
    }

    public function apply(QueryBuilder $qb, FilterDefinition $definition, string|array $value): void
    {
        $parameterName = $this->generateRandomParameterName();

        $qb->setParameter($parameterName, "%{$value}%");

        $alias = $this->getAlias($qb, $definition);

        $qb->andWhere("{$alias}.{$definition->field} LIKE :{$parameterName}");
    }
}
