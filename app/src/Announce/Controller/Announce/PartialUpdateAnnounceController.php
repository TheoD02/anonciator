<?php

declare(strict_types=1);

namespace App\Announce\Controller\Announce;

use App\Announce\Dto\Payload\PartialUpdateAnnouncePayload;
use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Announce')]
class PartialUpdateAnnounceController extends AbstractApiController
{
    #[Route('/{id}', methods: [Request::METHOD_PATCH])]
    #[SuccessResponse(dataFqcn: AnnounceResponse::class, description: 'Announce patched', groups: [ApiGroups::PATCH])]
    public function __invoke(
        #[MapRequestPayload] PartialUpdateAnnouncePayload $payload,
        AnnounceService $announceService,
        int $id,
    ): Response {
        $announce = $announceService->partialUpdateEntityFromPayload($id, $payload);

        return $this->successResponse($announce, target: AnnounceResponse::class, groups: [ApiGroups::PATCH]);
    }
}
