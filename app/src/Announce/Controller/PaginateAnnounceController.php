<?php

declare(strict_types=1);

namespace App\Announce\Controller;

use App\Announce\Dto\Filter\AnnounceFilterQuery;
use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use App\Shared\Api\PaginationFilterQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Stopwatch\Stopwatch;

class PaginateAnnounceController extends AbstractApiController
{
    #[Route('/', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: AnnounceResponse::class,
        description: 'Paginated list of announces',
        groups: [ApiGroups::GET_PAGINATED],
        paginated: true
    )]
    public function __invoke(
        AnnounceService $announceService,
        #[MapQueryString] AnnounceFilterQuery $query,
        #[MapQueryString] PaginationFilterQuery $paginationFilterQuery,
        Stopwatch $sw,
    ): Response
    {
        $announces = $announceService->paginate($query, $paginationFilterQuery);

        return $this->successResponse($announces, target: AnnounceResponse::class, groups: [ApiGroups::GET_PAGINATED]);
    }
}
