<?php

declare(strict_types=1);

namespace App\Announce\Controller\Announce;

use App\Announce\Dto\Payload\UpdateAnnouncePayload;
use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Announce')]
class UpdateAnnounceController extends AbstractApiController
{
    #[Route('/{id}', methods: [Request::METHOD_PUT])]
    #[SuccessResponse(
        dataFqcn: AnnounceResponse::class,
        description: 'Announce updated',
        groups: [GlobalApiGroups::PUT],
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_NOT_FOUND, description: 'Resource not found')]
    #[ErrorResponse(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid input')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(
        #[MapRequestPayload] UpdateAnnouncePayload $payload,
        AnnounceService                            $announceService,
        int                                        $id,
    ): Response
    {
        $announce = $announceService->updateEntityFromPayload($id, $payload);

        return $this->successResponse(
            data: $announce,
            target: AnnounceResponse::class,
            groups: [GlobalApiGroups::PUT],
        );
    }
}
