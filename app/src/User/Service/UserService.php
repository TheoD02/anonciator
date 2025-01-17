<?php

declare(strict_types=1);

namespace App\User\Service;

use App\Shared\Trait\EntityCrudServiceTrait;
use App\User\Entity\User;
use PHPUnit\Framework\Attributes\CodeCoverageIgnore;

/**
 * @template T of User
 *
 * @extends EntityCrudServiceTrait<T>
 */
class UserService
{
    use EntityCrudServiceTrait;

    public function getOneByEmail(string $email): ?User
    {
        return $this->getRepository()->findOneBy([
            'email' => $email,
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getEntityClass(): string
    {
        return User::class;
    }
}
