<?php

namespace App\Announce\Repository;

use App\Announce\Entity\AnnounceCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnnounceCategory>
 */
class AnnounceCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnounceCategory::class);
    }
}
