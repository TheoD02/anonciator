<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Service;

use App\Shared\Api\Doctrine\Filter\Adapter\FilterQueryDefinitionInterface;
use App\Shared\Api\Doctrine\Filter\Adapter\ORMQueryBuilderFilterQueryAwareInterface;
use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\FilterDefinitionBag;
use App\Shared\Api\Doctrine\Filter\Operator\OperatorInterface;
use App\Shared\Api\PaginationFilterQuery;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @template T of object
 */
class Paginator
{
    private readonly ?Request $request;

    public function __construct(
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function paginate(
        QueryBuilder $qb,
        ORMQueryBuilderFilterQueryAwareInterface|FilterQueryDefinitionInterface|null $queryBuilderFilterQueryAware = null,
        PaginationFilterQuery $paginationFilterQuery = new PaginationFilterQuery(),
    ): \Doctrine\ORM\Tools\Pagination\Paginator {
        if ($queryBuilderFilterQueryAware instanceof ORMQueryBuilderFilterQueryAwareInterface) {
            $queryBuilderFilterQueryAware->applyToORMQueryBuilder($qb);
        }

        if ($queryBuilderFilterQueryAware instanceof FilterQueryDefinitionInterface) {
            $this->handleFilterQueryDefinition($qb, $queryBuilderFilterQueryAware->definition());
        }

        $qb
            ->setFirstResult(($paginationFilterQuery->page - 1) * $paginationFilterQuery->limit)
            ->setMaxResults($paginationFilterQuery->limit)
        ;

        $query = $qb->getQuery();

        return new \App\Shared\Api\Doctrine\Pagination\Paginator($query, paginationFilterQuery: $paginationFilterQuery);
    }

    /**
     * @param FilterDefinitionBag<FilterDefinition> $definitionBag
     */
    private function handleFilterQueryDefinition(QueryBuilder $qb, FilterDefinitionBag $definitionBag): void
    {
        $queryParameters = $this->request->query->all();
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($definitionBag as $definition) {
            $publicName = $definition->publicName;
            $operators = $definition->operators;

            foreach ($operators as $operator) {
                /** @var OperatorInterface $operatorInstance */
                $operatorInstance = new $operator();
                $queryParameterName = \sprintf('[%s][%s]', $publicName, $operatorInstance->operator());
                $value = $accessor->getValue($queryParameters, $queryParameterName) ?? 'NOT_PRESENT';

                if ($value === 'NOT_PRESENT') {
                    continue;
                }

                $operatorInstance->apply($qb, $definition, $value);
            }
        }
    }
}
