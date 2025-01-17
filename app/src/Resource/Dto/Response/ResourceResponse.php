<?php

declare(strict_types=1);

namespace App\Resource\Dto\Response;

use App\Shared\Api\GlobalApiGroups;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;

class ResourceResponse
{
    #[Property(description: 'ID of the resource', example: 1)]
    #[Groups([GlobalApiGroups::GET_PAGINATED, GlobalApiGroups::POST])]
    public int $id;

    #[Property(description: 'Resource path', example: '/uploads/file-abc123.jpg')]
    #[Groups([GlobalApiGroups::GET_PAGINATED, GlobalApiGroups::POST])]
    public string $path;

    #[Property(description: 'Resource bucket', example: 'bucket-name')]
    #[Groups([GlobalApiGroups::GET_PAGINATED, GlobalApiGroups::POST])]
    public string $bucket;

    #[Property(description: 'Resource original name', example: 'original-filename.jpg')]
    #[Groups([GlobalApiGroups::GET_PAGINATED, GlobalApiGroups::POST])]
    public string $originalName;
}
