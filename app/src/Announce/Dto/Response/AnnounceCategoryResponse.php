<?php

declare(strict_types=1);

namespace App\Announce\Dto\Response;

use App\Shared\Api\GlobalApiGroups;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;

class AnnounceCategoryResponse
{
    #[Property(description: 'ID of the announce category', example: '1')]
    #[Groups([
        GlobalApiGroups::POST,
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    public int $id;

    #[Property(description: 'Name of the announce category', example: 'Cars')]
    #[Groups([
        GlobalApiGroups::POST,
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    public string $name;
}
