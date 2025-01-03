<?php

declare(strict_types=1);

namespace App\Announce\Service;

use App\Announce\Entity\Announce;

/**
 * @template T of Announce
 *
 * @extends EntityCrudServiceTrait<T>
 */
class AnnounceService
{
    use EntityCrudServiceTrait;

    protected function getEntityClass(): string
    {
        return Announce::class;
    }
}
