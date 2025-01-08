<?php

namespace App\Resource\Controller;

use App\Resource\Service\ResourceService;
use App\Shared\Api\AbstractApiController;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Resource')]
class DeleteResourceController extends AbstractApiController
{
    #[Route('{id}', methods: [Request::METHOD_DELETE])]
    public function __invoke(
        int $id,
        ResourceService $resourceService,
    ): Response
    {
        $resourceService->deleteEntityFromId($id);

        return $this->noContentResponse();
    }
}
