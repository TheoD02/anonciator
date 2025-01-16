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
    #[Property(description: 'Conversation ID')]
    #[Groups([ApiGroups::INIT, GlobalApiGroups::GET_ONE, GlobalApiGroups::GET_PAGINATED])]
    public int $id;

    #[Property(description: 'Name of the conversation')]
    #[Groups([GlobalApiGroups::GET_ONE, GlobalApiGroups::GET_PAGINATED])]
    public string $name;

    #[Property(description: 'Usernames of the user that initiated the conversation')]
    #[MapFrom(Conversation::class, transformer: 'source.getInitializedBy().getUsername()')]
    #[Groups([GlobalApiGroups::GET_ONE, GlobalApiGroups::GET_PAGINATED])]
    public string $initializedBy;

    #[Property(description: 'Usernames of the user that received the conversation')]
    #[MapFrom(Conversation::class, transformer: 'source.getReceiver().getUsername()')]
    #[Groups([GlobalApiGroups::GET_ONE, GlobalApiGroups::GET_PAGINATED])]
    public string $receivedBy;
}
