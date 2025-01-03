<?php

declare(strict_types=1);

namespace App\Announce\Controller\Announce;

use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Service\AnnounceService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Announce')]
class DeleteAnnounceController extends AbstractApiController
{
    #[Route('/{id}', methods: [Request::METHOD_DELETE])]
    #[SuccessResponse(dataFqcn: AnnounceResponse::class, description: 'Announce deleted', groups: [ApiGroups::DELETE])]
    public function __invoke(
        AnnounceService $announceService,
        int $id,
    ): Response
    {
        $announceService->deleteEntityFromId($id);

        return $this->noContentResponse();
    }
}
