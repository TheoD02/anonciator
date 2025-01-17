<?php

declare(strict_types=1);

namespace App\Announce\Controller\Category;

use App\Announce\Dto\Response\AnnounceCategoryResponse;
use App\Announce\Service\AnnounceCategoryService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'AnnounceCategory')]
class GetOneAnnounceCategoryController extends AbstractApiController
{
    #[Route('/categories/{id}', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: AnnounceCategoryResponse::class,
        description: 'Get one announce category',
        groups: [GlobalApiGroups::GET_ONE],
        paginated: false,
        statusCode: Response::HTTP_OK
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_NOT_FOUND, description: 'Resource not found')]
    #[ErrorResponse(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid input')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(int $id, AnnounceCategoryService $service): Response
    {
        $category = $service->getEntityById($id);

        return $this->successResponse(
            data: $category,
            target: AnnounceCategoryResponse::class,
            groups: [GlobalApiGroups::GET_ONE],
            status: Response::HTTP_OK
        );
    }
}
