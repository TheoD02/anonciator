<?php

declare(strict_types=1);

namespace App\Conversation\Service;

use App\Announce\Entity\Announce;
use App\Announce\Service\AnnounceService;
use App\Conversation\Entity\Conversation;
use App\Conversation\Repository\ConversationRepository;
use App\Shared\Trait\EntityCrudServiceTrait;
use App\User\Entity\User;
use App\User\Service\UserService;

class ConversationService
{
    use EntityCrudServiceTrait;

    public function __construct(
        private readonly AnnounceService        $announceService,
        private readonly UserService            $userService,
        private readonly ConversationRepository $conversationRepository,
    )
    {
    }

    public function initConversationOrGetExisting(int $announceId, User $loggedUser): Conversation
    {
        /** @var Announce $announce */
        $announce = $this->announceService->getEntityById($announceId, fail: true);
        $announceCreatorIdentifier = $announce->getCreatedBy();
        /** @var ?User $announceCreator */
        $announceCreator = $this->userService->getOneByEmail($announceCreatorIdentifier);
        if ($announceCreator === null) {
            throw new \RuntimeException('Announce creator does not exist');
        }

        if ($announceCreator->getId() === $loggedUser->getId()) {
            throw new \RuntimeException('Cannot create conversation to self');
        }

        $conversation = $this->conversationRepository->createQueryBuilder('c')
            ->where('c.announce = :announceId')
            ->andWhere('c.initializedBy = :userInitiatorId')
            ->andWhere('c.receiver = :userReceiverId')
            ->setParameter('announceId', $announceId)
            ->setParameter('userInitiatorId', $loggedUser->getId())
            ->setParameter('userReceiverId', $announceCreator->getId())
            ->getQuery()
            ->getOneOrNullResult();

        if ($conversation === null) {
            $conversation = new Conversation();
            $conversation->setName($announce->getTitle());
            $conversation->setAnnounce($announce);
            $conversation->setInitializedBy($loggedUser);
            $conversation->setReceiver($announceCreator);
            $this->em->persist($conversation);
            $this->em->flush();
        }

        return $conversation;
    }

    protected function getEntityClass(): string
    {
        return Conversation::class;
    }
}
