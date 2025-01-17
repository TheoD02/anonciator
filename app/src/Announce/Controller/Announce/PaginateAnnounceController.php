<?php

declare(strict_types=1);

namespace App\Announce\Controller\Announce;

use App\Announce\Dto\Filter\AnnounceFilterQuery;
use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Service\AnnounceService;
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

#[Tag(name: 'Announce')]
class PaginateAnnounceController extends AbstractApiController
{
    #[Route('/', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: AnnounceResponse::class,
        description: 'Paginated list of announces',
        groups: [GlobalApiGroups::GET_PAGINATED],
        paginated: true
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid input')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(
        AnnounceService                         $announceService,
        #[MapQueryString] AnnounceFilterQuery   $query,
        #[MapQueryString] PaginationFilterQuery $paginationFilterQuery,
    ): Response
    {
        $announces = $announceService->paginateEntities($query, $paginationFilterQuery);

        return $this->successResponse(
            data: $announces,
            target: AnnounceResponse::class,
            groups: [GlobalApiGroups::GET_PAGINATED],
        );
    }
}
