<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\Repository;

use App\Shared\Api\Doctrine\Filter\Adapter\FilterQueryDefinitionInterface;
use App\Shared\Api\Doctrine\Filter\Adapter\ORMQueryBuilderFilterQueryAwareInterface;
use App\Shared\Api\Doctrine\Service\Paginator;
use App\Shared\Api\PaginationFilterQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @template T of object
 *
 * @extends ServiceEntityRepository<T>
 */
abstract class AbstractEntityRepository extends ServiceEntityRepository
{
    /**
     * @var Paginator<T>
     */
    private Paginator $paginator;

    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, $this->getEntityFqcn());
    }

    /**
     * @return class-string<T>
     */
    abstract public function getEntityFqcn(): string;

    #[Required]
    public function setPaginator(Paginator $paginator): void
    {
        $this->paginator = $paginator;
    }

    public function paginate(
        ORMQueryBuilderFilterQueryAwareInterface|FilterQueryDefinitionInterface|null $queryBuilderFilterQueryAware = null,
        PaginationFilterQuery $paginationFilterQuery = new PaginationFilterQuery(),
    ): \App\Shared\Api\Doctrine\Pagination\Paginator {
        $qb = $this->createPaginationQueryBuilder();

        return $this->paginator->paginate($qb, $queryBuilderFilterQueryAware, $paginationFilterQuery);
    }

    protected function createPaginationQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('e');
    }
}
