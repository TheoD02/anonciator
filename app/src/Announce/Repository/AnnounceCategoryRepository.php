<?php

declare(strict_types=1);

namespace App\Announce\Repository;

use App\Announce\Entity\AnnounceCategory;
use App\Shared\Doctrine\Repository\AbstractEntityRepository;

/**
 * @extends AbstractEntityRepository<AnnounceCategory>
 */
class AnnounceCategoryRepository extends AbstractEntityRepository
{
    public function getEntityFqcn(): string
    {
        return AnnounceCategory::class;
    }
}
