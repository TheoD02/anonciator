<?php

namespace App\Resource\Controller;

use App\Resource\Dto\Response\ResourceResponse;
use App\Resource\Service\ResourceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use App\Shared\Api\PaginationFilterQuery;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Resource')]
class PaginateResourceController extends AbstractApiController
{
    #[Route('', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: ResourceResponse::class,
        description: 'Paginate resources',
        groups: [ApiGroups::GET_PAGINATED],
        statusCode: Response::HTTP_OK
    )]
    public function __invoke(
        #[MapQueryString] PaginationFilterQuery $paginationFilterQuery,
        ResourceService $resourceService,
    ): Response
    {
        $resources = $resourceService->paginateEntities(paginationFilterQuery: $paginationFilterQuery);

        return $this->successResponse(
            data: $resources,
            target: ResourceResponse::class,
            groups: [ApiGroups::GET_PAGINATED],
        );
    }
}
