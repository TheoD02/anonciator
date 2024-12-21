<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\Repository;

use App\Shared\Api\Doctrine\Filter\Adapter\FilterQueryDefinitionInterface;
use App\Shared\Api\Doctrine\Filter\Adapter\ORMQueryBuilderFilterQueryAwareInterface;
use App\Shared\Api\Doctrine\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    /**
     * @return class-string<T>
     */
    abstract public function getEntityFqcn(): string;

    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, $this->getEntityFqcn());
    }

    #[Required]
    public function setPaginator(Paginator $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * @return array<T>
     */
    public function paginate(
        ORMQueryBuilderFilterQueryAwareInterface|FilterQueryDefinitionInterface|null $queryBuilderFilterQueryAware = null,
        int $page = 1,
        int $limit = 30,
    ): array {
        $qb = $this->createQueryBuilder('e');

        return $this->paginator->paginate($qb, $queryBuilderFilterQueryAware, $page, $limit);
    }
}
