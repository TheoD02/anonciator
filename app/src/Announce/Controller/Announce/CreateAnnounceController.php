<?php

declare(strict_types=1);

namespace App\Announce\Controller\Announce;

use App\Announce\Dto\Payload\CreateAnnouncePayload;
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
class CreateAnnounceController extends AbstractApiController
{
    #[Route('', methods: [Request::METHOD_POST])]
    #[SuccessResponse(
        dataFqcn: AnnounceResponse::class,
        description: 'Announce created',
        groups: [GlobalApiGroups::POST],
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid input')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(
        #[MapRequestPayload] CreateAnnouncePayload $payload,
        AnnounceService                            $announceService,
    ): Response
    {
        $announce = $announceService->createEntityFromPayload($payload);

        return $this->successResponse(
            data: $announce,
            target: AnnounceResponse::class,
            groups: [GlobalApiGroups::POST],
        );
    }
}
