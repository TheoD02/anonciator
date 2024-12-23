<?php

declare(strict_types=1);

namespace App\Announce\Controller;

use App\Announce\Dto\Payload\UpdateAnnouncePayload;
use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class UpdateAnnounceController extends AbstractApiController
{
    #[Route('/{id}', methods: [Request::METHOD_PUT])]
    #[SuccessResponse(dataFqcn: AnnounceResponse::class, description: 'Announce updated', groups: [ApiGroups::PUT])]
    public function put(
        #[MapRequestPayload] UpdateAnnouncePayload $payload,
        AnnounceService $announceService,
        int $id,
    ): Response
    {
        $announce = $announceService->updateAnnounceFromPayload($id, $payload);

        return $this->successResponse($announce, target: AnnounceResponse::class, groups: [ApiGroups::PUT]);
    }
}
