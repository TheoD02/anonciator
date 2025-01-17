<?php

declare(strict_types=1);

namespace App\Conversation\Dto\Response;

use App\Conversation\Entity\Message;
use App\Shared\Api\GlobalApiGroups;
use AutoMapper\Attribute\MapFrom;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;

class MessageResponse
{
    #[Property(description: 'Message ID', example: 1)]
    #[Groups([GlobalApiGroups::GET_PAGINATED, GlobalApiGroups::POST])]
    public int $id;

    #[Property(description: 'Message content', example: 'Hello, how are you?')]
    #[Groups([GlobalApiGroups::GET_PAGINATED])]
    public string $content;

    #[Property(description: 'Message was sent at', format: 'date-time', example: '2021-01-01T00:00:00+00:00')]
    #[MapFrom(Message::class, property: 'createdAt')]
    #[Groups([GlobalApiGroups::GET_PAGINATED])]
    public \DateTimeImmutable $sentAt;

    #[Property(description: 'Message was read at', format: 'date-time', example: '2021-01-01T00:00:00+00:00')]
    #[Groups([GlobalApiGroups::GET_PAGINATED])]
    public ?\DateTimeImmutable $wasReadAt = null;

    #[Property(description: 'Email of the user that sent the message', example: 'user1@mail.com')]
    #[Groups([GlobalApiGroups::GET_PAGINATED])]
    public string $sentBy;

    #[Property(description: 'Email of the user that received the message', example: 'user2@mail.com')]
    #[Groups([GlobalApiGroups::GET_PAGINATED])]
    public string $sentTo;
}
