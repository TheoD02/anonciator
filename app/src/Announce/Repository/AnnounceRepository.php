<?php

declare(strict_types=1);

namespace App\Announce\Repository;

use App\Announce\Entity\Announce;
use App\Shared\Doctrine\Repository\AbstractEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends AbstractEntityRepository<Announce>
 */
class AnnounceRepository extends AbstractEntityRepository
{
    public function getEntityFqcn(): string
    {
        return Announce::class;
    }

    public function createPaginationQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            // Optimise query by joining related entities directly
            ->leftJoin('e.category', 'category')
            ->leftJoin('e.photos', 'photos')
            ->addSelect('category', 'photos');
    }
}
