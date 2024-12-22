<?php

declare(strict_types=1);

namespace App\Announce\Controller;

use App\Announce\Dto\Filter\AnnounceFilterQuery;
use App\Announce\Dto\Payload\CreateAnnouncePayload;
use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use App\Shared\Api\PaginationFilterQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Stopwatch\Stopwatch;

class CreateAnnounceController extends AbstractApiController
{
    #[Route('/create', methods: ['POST'])]
    #[SuccessResponse(dataFqcn: AnnounceResponse::class, description: 'Announce created', groups: [ApiGroups::CREATE])]
    public function create(
        #[MapRequestPayload] CreateAnnouncePayload $payload,
        AnnounceService $announceService,
    ): Response
    {
        $announce = $announceService->createAnnounceFromPayload($payload);

        return $this->successResponse($announce, target: AnnounceResponse::class, groups: [ApiGroups::CREATE]);
    }


    #[Route('/list', methods: ['GET'])]
    #[SuccessResponse(dataFqcn: AnnounceResponse::class, description: 'Announce created', groups: [ApiGroups::GET_PAGINATED], paginated: true)]
    public function list(
        AnnounceService $announceService,
        #[MapQueryString] AnnounceFilterQuery $query,
        #[MapQueryString] PaginationFilterQuery $paginationFilterQuery,
        Stopwatch $sw
    ): Response
    {
        $announces = $announceService->paginate($query, $paginationFilterQuery);

        return $this->successResponse($announces, target: AnnounceResponse::class, groups: [ApiGroups::GET_PAGINATED]);
    }
}
