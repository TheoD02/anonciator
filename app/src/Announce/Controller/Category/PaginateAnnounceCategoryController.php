<?php

declare(strict_types=1);

namespace App\Announce\Controller\Category;

use App\Announce\Dto\Filter\AnnounceCategoryFilterQuery;
use App\Announce\Dto\Response\AnnounceCategoryResponse;
use App\Announce\Service\AnnounceCategoryService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use App\Shared\Api\PaginationFilterQuery;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'AnnounceCategory')]
class PaginateAnnounceCategoryController extends AbstractApiController
{
    #[Route('/categories', methods: [Request::METHOD_GET], priority: 1)]
    #[SuccessResponse(
        dataFqcn: AnnounceCategoryResponse::class,
        description: 'Paginate Announce Categories',
        groups: [GlobalApiGroups::GET_PAGINATED],
        paginated: true,
        statusCode: Response::HTTP_OK
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid input')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(
        #[MapQueryString] AnnounceCategoryFilterQuery $filterQuery,
        #[MapQueryString] PaginationFilterQuery $paginationFilterQuery,
        AnnounceCategoryService $service,
    ): Response {
        $category = $service->paginateEntities($filterQuery, $paginationFilterQuery);

        return $this->successResponse(
            data: $category,
            target: AnnounceCategoryResponse::class,
            groups: [GlobalApiGroups::GET_ONE],
            status: Response::HTTP_OK
        );
    }
}
