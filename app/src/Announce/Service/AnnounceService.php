<?php

declare(strict_types=1);

namespace App\Announce\Service;

use App\Announce\Entity\Announce;
use App\Shared\Trait\EntityCrudServiceTrait;

/**
 * @template T of Announce
 *
 * @extends EntityCrudServiceTrait<T>
 */
class AnnounceService
{
    use EntityCrudServiceTrait;

    /**
     * @codeCoverageIgnore
     */
    protected function getEntityClass(): string
    {
        return Announce::class;
    }
}
