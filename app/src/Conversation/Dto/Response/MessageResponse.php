<?php

declare(strict_types=1);

namespace App\Conversation\Dto\Response;

use App\Conversation\Entity\Message;
use App\Shared\Api\ApiGroups;
use AutoMapper\Attribute\MapFrom;
use Symfony\Component\Serializer\Attribute\Groups;

class MessageResponse
{
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::POST])]
    public int $id;

    #[Groups([ApiGroups::GET_PAGINATED])]
    public string $content;

    #[MapFrom(Message::class, property: 'createdAt')]
    #[Groups([ApiGroups::GET_PAGINATED])]
    public \DateTimeImmutable $sentAt;

    #[Groups([ApiGroups::GET_PAGINATED])]
    public ?\DateTimeImmutable $wasReadAt = null;

    #[Groups([ApiGroups::GET_PAGINATED])]
    public string $sentBy;

    #[Groups([ApiGroups::GET_PAGINATED])]
    public string $sentTo;
}
