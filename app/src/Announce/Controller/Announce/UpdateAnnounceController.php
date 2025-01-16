<?php

declare(strict_types=1);

namespace App\Announce\Controller\Announce;

use App\Announce\Dto\Payload\UpdateAnnouncePayload;
use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Announce')]
class UpdateAnnounceController extends AbstractApiController
{
    #[Route('/{id}', methods: [Request::METHOD_PUT])]
    #[SuccessResponse(dataFqcn: AnnounceResponse::class, description: 'Announce updated', groups: [GlobalApiGroups::PUT])]
    public function __invoke(
        #[MapRequestPayload] UpdateAnnouncePayload $payload,
        AnnounceService                            $announceService,
        int                                        $id,
    ): Response
    {
        $announce = $announceService->updateEntityFromPayload($id, $payload);

        return $this->successResponse($announce, target: AnnounceResponse::class, groups: [GlobalApiGroups::PUT]);
    }
}
