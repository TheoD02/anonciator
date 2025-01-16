<?php

declare(strict_types=1);

namespace App\Announce\Controller\Category;

use App\Announce\Dto\Response\AnnounceCategoryResponse;
use App\Announce\Service\AnnounceCategoryService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
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
        statusCode: 200
    )]
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
