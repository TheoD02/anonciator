<?php

declare(strict_types=1);

namespace App\User\Repository;

use App\Shared\Doctrine\Repository\AbstractEntityRepository;
use App\User\Entity\User;

/**
 * @extends AbstractEntityRepository<User>
 */
class UserRepository extends AbstractEntityRepository
{
    public function getEntityFqcn(): string
    {
        return User::class;
    }
}
