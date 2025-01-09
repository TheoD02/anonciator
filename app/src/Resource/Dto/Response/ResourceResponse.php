<?php

declare(strict_types=1);

namespace App\Resource\Dto\Response;

use App\Shared\Api\ApiGroups;
use Symfony\Component\Serializer\Attribute\Groups;

class ResourceResponse
{
    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::POST])]
    public string $id;

    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::POST])]
    public string $path;

    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::POST])]
    public string $bucket;

    #[Groups([ApiGroups::GET_PAGINATED, ApiGroups::POST])]
    public string $originalName;
}
