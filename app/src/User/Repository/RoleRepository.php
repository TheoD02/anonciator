<?php

declare(strict_types=1);

namespace App\User\Repository;

use App\Shared\Doctrine\Repository\AbstractEntityRepository;
use App\User\Entity\Role;

/**
 * @extends AbstractEntityRepository<Role>
 */
class RoleRepository extends AbstractEntityRepository
{
    /**
     * @codeCoverageIgnore
     */
    public function getEntityFqcn(): string
    {
        return Role::class;
    }
}
