<?php

declare(strict_types=1);

namespace App\User\Repository;

use App\Shared\Doctrine\Repository\AbstractEntityRepository;
use App\User\Entity\GroupRole;

/**
 * @extends AbstractEntityRepository<GroupRole>
 */
class GroupRoleRepository extends AbstractEntityRepository
{
    /**
     * @codeCoverageIgnore
     */

    /**
     * @codeCoverageIgnore
     */
    public function getEntityFqcn(): string
    {
        return GroupRole::class;
    }
}
