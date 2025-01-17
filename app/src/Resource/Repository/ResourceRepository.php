<?php

declare(strict_types=1);

namespace App\Resource\Repository;

use App\Resource\Entity\Resource;
use App\Shared\Doctrine\Repository\AbstractEntityRepository;

/**
 * @extends AbstractEntityRepository<Resource>
 */
class ResourceRepository extends AbstractEntityRepository
{
    /**
     * @codeCoverageIgnore
     */
    public function getEntityFqcn(): string
    {
        return Resource::class;
    }
}
