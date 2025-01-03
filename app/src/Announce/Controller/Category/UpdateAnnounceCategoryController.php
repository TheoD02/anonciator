<?php

declare(strict_types=1);

namespace App\Announce\Controller\Category;

use App\Announce\Dto\Payload\UpdateAnnounceCategoryPayload;
use App\Announce\Dto\Response\AnnounceCategoryResponse;
use App\Announce\Service\AnnounceCategoryService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'AnnounceCategory')]
class UpdateAnnounceCategoryController extends AbstractApiController
{
    #[Route('/categories/{id}', methods: [Request::METHOD_PUT])]
    #[SuccessResponse(
        dataFqcn: AnnounceCategoryResponse::class,
        description: 'Announce category updated',
        groups: [ApiGroups::PUT],
        paginated: false,
        statusCode: 200
    )]
    public function __invoke(
        int $id,
        #[MapRequestPayload] UpdateAnnounceCategoryPayload $payload,
        AnnounceCategoryService $service,
    ): Response
    {
        $category = $service->updateEntityFromPayload($id, $payload);

        return $this->successResponse(
            data: $category,
            target: AnnounceCategoryResponse::class,
            groups: [ApiGroups::POST],
            status: Response::HTTP_OK
        );
    }
}
