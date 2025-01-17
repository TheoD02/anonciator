<?php

declare(strict_types=1);

namespace App\Conversation\Service;

use App\Announce\Entity\Announce;
use App\Announce\Service\AnnounceService;
use App\Conversation\Dto\Payload\SendMessagePayload;
use App\Conversation\Entity\Conversation;
use App\Conversation\Entity\Message;
use App\Shared\Api\PaginationFilterQuery;
use App\Shared\Trait\EntityCrudServiceTrait;
use App\User\Entity\User;
use App\User\Service\UserService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MessageService
{
    use EntityCrudServiceTrait;

    public function __construct(
        private readonly ConversationService $conversationService,
        private readonly AnnounceService $announceService,
        private readonly UserService $userService,
    ) {
    }

    public function createEntityFromPayload(int $id, SendMessagePayload $payload, User $user): Message
    {
        /** @var Conversation $conversation */
        $conversation = $this->conversationService->getEntityById($id, fail: true);

        /** @var ?Announce $announce */
        $announce = $conversation->getAnnounce();
        if ($announce === null) {
            throw new NotFoundHttpException('Announce not found');
        }

        $announceCreator = $this->userService->getOneByEmail($announce->getCreatedBy());

        $message = new Message();
        $message->setContent($payload->content);
        $message->setSentTo(
            $user === $announceCreator ? $conversation->getInitializedBy() : $conversation->getReceiver()
        );
        $message->setSentBy($user);
        $message->setConversation($conversation);

        return $this->createEntity($message);
    }

    public function getMessagesForConversation(int $id, PaginationFilterQuery $paginationFilterQuery)
    {
        return $this->getRepository()
            ->getMessagesForConversation($id, $paginationFilterQuery)
        ;
    }

    protected function getEntityClass(): string
    {
        return Message::class;
    }
}
