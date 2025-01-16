<?php

declare(strict_types=1);

namespace App\Resource\Controller;

use App\Resource\Dto\Filter\PaginateResourceFilterQuery;
use App\Resource\Dto\Response\ResourceResponse;
use App\Resource\Service\ResourceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
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
        groups: [GlobalApiGroups::GET_PAGINATED],
        statusCode: Response::HTTP_OK
    )]
    public function __invoke(
        #[MapQueryString] PaginateResourceFilterQuery $filterQuery,
        #[MapQueryString] PaginationFilterQuery       $paginationFilterQuery,
        ResourceService                               $resourceService,
    ): Response
    {
        $resources = $resourceService->paginateEntities($filterQuery, paginationFilterQuery: $paginationFilterQuery);

        return $this->successResponse(
            data: $resources,
            target: ResourceResponse::class,
            groups: [GlobalApiGroups::GET_PAGINATED],
        );
    }
}
