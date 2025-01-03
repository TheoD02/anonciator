<?php

declare(strict_types=1);

namespace App\Message\Repository;

use App\Message\Entity\Message;
use App\Shared\Doctrine\Repository\AbstractEntityRepository;

/**
 * @extends AbstractEntityRepository<Message>
 */
class MessageRepository extends AbstractEntityRepository
{
    public function getEntityFqcn(): string
    {
        return Message::class;
    }
}
