<?php

declare(strict_types=1);

namespace App\Message\Repository;

use App\Message\Entity\Conversation;
use App\Shared\Doctrine\Repository\AbstractEntityRepository;

/**
 * @extends AbstractEntityRepository<Conversation>
 */
class ConversationRepository extends AbstractEntityRepository
{
    public function getEntityFqcn(): string
    {
        return Conversation::class;
    }
}
