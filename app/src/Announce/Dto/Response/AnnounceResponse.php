<?php

namespace App\Announce\Dto\Response;

use App\Announce\AnnounceStatus;
use App\Announce\Dto\Visibility;
use App\Shared\Api\ApiGroups;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;

class AnnounceResponse
{
    #[Property(description: 'ID of the announce', example: '1')]
    #[Groups([ApiGroups::CREATE, ApiGroups::GET_PAGINATED, ApiGroups::GET_ONE, ApiGroups::UPDATE, ApiGroups::DELETE, ApiGroups::PATCH])]
    public int $id;

    #[Property(description: 'Title of the announce', example: 'This is a title')]
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::GET_ONE, ApiGroups::UPDATE, ApiGroups::DELETE, ApiGroups::PATCH])]
    public string $title;

    #[Property(description: 'Description of the announce', example: 'This is a description')]
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::GET_ONE, ApiGroups::UPDATE, ApiGroups::DELETE, ApiGroups::PATCH])]
    public string $description;

    #[Property(description: 'Price of the announce', example: '100.00')]
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::GET_ONE, ApiGroups::UPDATE, ApiGroups::DELETE, ApiGroups::PATCH])]
    #[Visibility(external: false, internal: true)]
    public string $price;

    #[Property(description: 'Category ID of the announce', example: '1')]
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::GET_ONE, ApiGroups::UPDATE, ApiGroups::DELETE, ApiGroups::PATCH])]
    public int $categoryId;

    #[Property(description: 'Location of the announce', example: '41.0987')]
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::GET_ONE, ApiGroups::UPDATE, ApiGroups::DELETE, ApiGroups::PATCH])]
    public string $location;

    #[Property(description: 'Status of the announce', example: 'draft')]
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::GET_ONE, ApiGroups::UPDATE, ApiGroups::DELETE, ApiGroups::PATCH])]
    public AnnounceStatus $status;

    #[Property(description: 'Photo IDs of the announce', example: [1, 2, 3])]
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::GET_ONE, ApiGroups::UPDATE, ApiGroups::DELETE, ApiGroups::PATCH])]
    public array $photoIds;
}
