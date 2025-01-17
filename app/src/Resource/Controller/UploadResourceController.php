<?php

declare(strict_types=1);

namespace App\Resource\Controller;

use App\Resource\Dto\Response\ResourceResponse;
use App\Resource\Service\ResourceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Resource')]
class UploadResourceController extends AbstractApiController
{
    #[Route('', methods: [Request::METHOD_POST])]
    #[SuccessResponse(
        dataFqcn: ResourceResponse::class,
        description: 'Resource created',
        groups: [GlobalApiGroups::POST],
        statusCode: Response::HTTP_CREATED
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(Request $request, ResourceService $resourceService): Response
    {
        /** @var array<UploadedFile> $files */
        $files = [...$request->files->all('files'), $request->files->get('file')];
        $files = array_filter($files);

        $resources = $resourceService->createManyResources(...$files);

        return $this->successResponse(
            data: $request->files->has('file') ? $resources[0] : $resources,
            target: ResourceResponse::class,
            groups: [GlobalApiGroups::POST],
            status: Response::HTTP_CREATED
        );
    }
}
