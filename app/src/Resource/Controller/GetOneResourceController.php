<?php

declare(strict_types=1);

namespace App\Resource\Controller;

use App\Resource\Entity\Resource;
use App\Resource\Service\ResourceService;
use App\Shared\Api\AbstractApiController;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Resource')]
class GetOneResourceController extends AbstractApiController
{
    #[Route('/{id}', methods: [Request::METHOD_GET])]
    public function __invoke(int $id, KernelInterface $kernel, ResourceService $resourceService): Response
    {
        /** @var resource $resource */
        $resource = $resourceService->getEntityById($id, fail: true);

        return new BinaryFileResponse("{$kernel->getProjectDir()}/public{$resource->getPath()}");
    }
}
