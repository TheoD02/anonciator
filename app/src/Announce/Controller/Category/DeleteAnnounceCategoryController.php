<?php

declare(strict_types=1);

namespace App\Announce\Controller\Category;

use App\Announce\Service\AnnounceCategoryService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use App\Shared\Api\Nelmio\Attribute\NoContentResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'AnnounceCategory')]
class DeleteAnnounceCategoryController extends AbstractApiController
{
    #[Route('/categories/{id}', methods: [Request::METHOD_DELETE])]
    #[NoContentResponse(description: 'Resource deleted')]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_NOT_FOUND, description: 'Resource not found')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(int $id, AnnounceCategoryService $service): Response
    {
        $service->deleteEntityFromId($id);

        return $this->noContentResponse();
    }
}
