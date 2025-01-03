<?php

declare(strict_types=1);

namespace App\Announce\Dto\Response;

use App\Shared\Api\ApiGroups;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;

class AnnounceCategoryResponse
{
    #[Property(description: 'ID of the announce category', example: '1')]
    #[Groups([
        ApiGroups::POST,
        ApiGroups::GET_PAGINATED,
        ApiGroups::GET_ONE,
        ApiGroups::PUT,
        ApiGroups::DELETE,
        ApiGroups::PATCH,
    ])]
    public int $id;

    #[Property(description: 'Name of the announce category', example: 'Cars')]
    #[Groups([
        ApiGroups::POST,
        ApiGroups::GET_PAGINATED,
        ApiGroups::GET_ONE,
        ApiGroups::PUT,
        ApiGroups::DELETE,
        ApiGroups::PATCH,
    ])]
    public string $name;
}
