<?php

declare(strict_types=1);

namespace App\Resource\Dto\Response;

use App\Shared\Api\GlobalApiGroups;
use Symfony\Component\Serializer\Attribute\Groups;

class ResourceResponse
{
    #[Groups([GlobalApiGroups::GET_PAGINATED, GlobalApiGroups::POST])]
    public string $id;

    #[Groups([GlobalApiGroups::GET_PAGINATED, GlobalApiGroups::POST])]
    public string $path;

    #[Groups([GlobalApiGroups::GET_PAGINATED, GlobalApiGroups::POST])]
    public string $bucket;

    #[Groups([GlobalApiGroups::GET_PAGINATED, GlobalApiGroups::POST])]
    public string $originalName;
}
