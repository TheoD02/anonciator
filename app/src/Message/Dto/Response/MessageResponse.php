<?php

declare(strict_types=1);

namespace App\Message\Dto\Response;

use App\Shared\Api\ApiGroups;
use Symfony\Component\Serializer\Attribute\Groups;

class MessageResponse
{
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::POST])]
    public string $id;

    #[Groups([ApiGroups::GET_PAGINATED])]
    public string $content;
}
