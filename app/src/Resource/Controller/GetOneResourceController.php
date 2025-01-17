<?php

declare(strict_types=1);

namespace App\Resource\Controller;

use App\Resource\Entity\Resource;
use App\Resource\Service\ResourceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Resource')]
class GetOneResourceController extends AbstractApiController
{
    #[Route('/{id}', methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: 'Get resource by ID (file download)',
        content: new OA\MediaType(mediaType: 'application/octet-stream')
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_NOT_FOUND, description: 'Resource not found')]
    #[ErrorResponse(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid input')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(int $id, KernelInterface $kernel, ResourceService $resourceService): Response
    {
        /** @var resource $resource */
        $resource = $resourceService->getEntityById($id, fail: true);

        return new BinaryFileResponse("{$kernel->getProjectDir()}/public{$resource->getPath()}");
    }
}
