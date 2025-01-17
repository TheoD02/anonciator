<?php

declare(strict_types=1);

namespace App\Conversation\Dto\Response;

use App\Conversation\Entity\Conversation;
use App\Conversation\Enum\ApiGroups;
use App\Shared\Api\GlobalApiGroups;
use AutoMapper\Attribute\MapFrom;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;

class ConversationResponse
{
    #[Property(description: 'Conversation ID', example: 1)]
    #[Groups([ApiGroups::INIT, GlobalApiGroups::GET_ONE, GlobalApiGroups::GET_PAGINATED])]
    public int $id;

    #[Property(description: 'Name of the conversation', example: 'Conversation name')]
    #[Groups([GlobalApiGroups::GET_ONE, GlobalApiGroups::GET_PAGINATED])]
    public string $name;

    #[Property(description: 'Usernames of the user that initiated the conversation', example: 'user1@mail.com')]
    #[MapFrom(Conversation::class, transformer: 'source.getInitializedBy().getEmail()')]
    #[Groups([GlobalApiGroups::GET_ONE, GlobalApiGroups::GET_PAGINATED])]
    public string $initializedBy;

    #[Property(description: 'Usernames of the user that received the conversation', example: 'user2@mail.com')]
    #[MapFrom(Conversation::class, transformer: 'source.getReceiver().getEmail()')]
    #[Groups([GlobalApiGroups::GET_ONE, GlobalApiGroups::GET_PAGINATED])]
    public string $receivedBy;
}
