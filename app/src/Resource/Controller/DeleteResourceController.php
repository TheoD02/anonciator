<?php

declare(strict_types=1);

namespace App\Resource\Controller;

use App\Resource\Service\ResourceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use App\Shared\Api\Nelmio\Attribute\NoContentResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Resource')]
class DeleteResourceController extends AbstractApiController
{
    #[Route('{id}', methods: [Request::METHOD_DELETE])]
    #[NoContentResponse(description: 'Resource deleted')]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_NOT_FOUND, description: 'Resource not found')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(int $id, ResourceService $resourceService): Response
    {
        $resourceService->deleteEntityFromId($id);

        return $this->noContentResponse();
    }
}
