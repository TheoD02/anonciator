<?php

declare(strict_types=1);

namespace App\Conversation\Service;

use App\Announce\Entity\Announce;
use App\Announce\Service\AnnounceService;
use App\Conversation\Builder\ConversationBuilder;
use App\Conversation\Entity\Conversation;
use App\Conversation\Repository\ConversationRepository;
use App\Shared\Trait\EntityCrudServiceTrait;
use App\User\Entity\User;
use App\User\Service\UserService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @template T of Conversation
 *
 * @extends EntityCrudServiceTrait<T>
 */
class ConversationService
{
    use EntityCrudServiceTrait;

    public function __construct(
        private readonly AnnounceService $announceService,
        private readonly UserService $userService,
        private readonly ConversationRepository $conversationRepository,
    ) {
    }

    public function initConversationOrGetExisting(int $announceId, User $loggedUser): Conversation
    {
        /** @var Announce $announce */
        $announce = $this->announceService->getEntityById($announceId, fail: true);
        $announceCreatorIdentifier = $announce->getCreatedBy();

        /** @var ?User $announceCreator */
        $announceCreator = $this->userService->getOneByEmail($announceCreatorIdentifier);
        if ($announceCreator === null) {
            throw new NotFoundHttpException('Announce creator does not exist');
        }

        if ($announceCreator->getEmail() === $loggedUser->getEmail()) {
            throw new UnprocessableEntityHttpException('Cannot create conversation to self');
        }

        $conversation = $this->conversationRepository->getConversationMatchingAnnounceAndUser(
            announceId: $announceId,
            userInitiatorId: $loggedUser->getId(),
            userReceiverId: $announceCreator->getId()
        );

        if (! $conversation instanceof Conversation) {
            $conversation = ConversationBuilder::new()
                ->withAnnounce($announce)
                ->withInitializedBy($loggedUser)
                ->withReceiver($announceCreator)
                ->build()
            ;

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
