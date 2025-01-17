<?php

declare(strict_types=1);

namespace App\Announce\Controller\Announce;

use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Announce')]
class GetOneAnnounceController extends AbstractApiController
{
    #[Route('/{id}', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: AnnounceResponse::class,
        description: 'Get announce by ID',
        groups: [GlobalApiGroups::GET_ONE],
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_NOT_FOUND, description: 'Resource not found')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(AnnounceService $announceService, int $id): Response
    {
        $announce = $announceService->getEntityById($id);

        return $this->successResponse(
            data: $announce,
            target: AnnounceResponse::class,
            groups: [GlobalApiGroups::GET_ONE],
        );
    }
}
