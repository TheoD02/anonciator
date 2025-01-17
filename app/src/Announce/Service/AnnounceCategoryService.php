<?php

declare(strict_types=1);

namespace App\Announce\Service;

use App\Announce\Entity\AnnounceCategory;
use App\Shared\Trait\EntityCrudServiceTrait;

/**
 * @template T of AnnounceCategory
 *
 * @extends EntityCrudServiceTrait<T>
 */
class AnnounceCategoryService
{
    use EntityCrudServiceTrait;

    protected function getEntityClass(): string
    {
        return AnnounceCategory::class;
    }
}
