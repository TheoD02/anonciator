<?php

declare(strict_types=1);

namespace App\Announce\Controller\Category;

use App\Announce\Dto\Payload\CreateAnnounceCategoryPayload;
use App\Announce\Dto\Response\AnnounceCategoryResponse;
use App\Announce\Service\AnnounceCategoryService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'AnnounceCategory')]
class CreateAnnounceCategoryController extends AbstractApiController
{
    #[Route('/categories', methods: [Request::METHOD_POST])]
    #[SuccessResponse(
        dataFqcn: AnnounceCategoryResponse::class,
        description: 'Announce category created',
        groups: [GlobalApiGroups::POST],
        paginated: false,
        statusCode: Response::HTTP_CREATED
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid input')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(
        #[MapRequestPayload] CreateAnnounceCategoryPayload $payload,
        AnnounceCategoryService $service,
    ): Response {
        $category = $service->createEntityFromPayload($payload);

        return $this->successResponse(
            data: $category,
            target: AnnounceCategoryResponse::class,
            groups: [GlobalApiGroups::POST],
            status: Response::HTTP_CREATED
        );
    }
}
