<?php

namespace App\Message\Service;

use App\Announce\Entity\Announce;
use App\Announce\Service\AnnounceService;
use App\Announce\Service\EntityCrudServiceTrait;
use App\Message\Dto\Payload\SendMessagePayload;
use App\Message\Entity\Message;
use Symfony\Bundle\SecurityBundle\Security;

class MessageService
{
    use EntityCrudServiceTrait;

    public function __construct(
        private readonly AnnounceService $announceService,
        private readonly Security $security,
    )
    {
    }

    public function createEntityFromPayload(SendMessagePayload $payload): object
    {
        /** @var Announce $announce */
        $announce = $this->announceService->getEntityById($payload->announceId, fail: true);

        $message = new Message();
        $message->setContent($payload->content);
        $message->setAnnounce($announce);
        $message->setSentTo($this->security->getUser());
        $message->setSentBy($this->security->getUser());

        return $this->createEntity($message);
    }

    protected function getEntityClass(): string
    {
        return Message::class;
    }
}
