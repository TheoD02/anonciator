<?php

declare(strict_types=1);

namespace App\Conversation\Repository;

use App\Conversation\Entity\Conversation;
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

    public function getConversationMatchingAnnounceAndUser(int $announceId, int $userInitiatorId, int $userReceiverId): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->where('c.announce = :announceId')
            ->andWhere('c.initializedBy = :userInitiatorId')
            ->andWhere('c.receiver = :userReceiverId')
            ->setParameter('announceId', $announceId)
            ->setParameter('userInitiatorId', $userInitiatorId)
            ->setParameter('userReceiverId', $userReceiverId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
