<?php

declare(strict_types=1);

namespace App\Message\Service;

use App\Announce\Entity\Announce;
use App\Announce\Service\AnnounceService;
use App\Announce\Service\EntityCrudServiceTrait;
use App\Message\Dto\Payload\SendMessagePayload;
use App\Message\Entity\Message;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageService
{
    use EntityCrudServiceTrait;

    public function __construct(
        private readonly AnnounceService $announceService,
        private readonly Security $security,
    ) {
    }

    public function createEntityFromPayload(SendMessagePayload $payload): object
    {
        $user = $this->security->getUser();

        if (! $user instanceof UserInterface) {
            throw new \RuntimeException('User not authenticated');
        }

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
