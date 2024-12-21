<?php

namespace App\Announce\Controller;

use App\Announce\Dto\Payload\CreateAnnouncePayload;
use App\Shared\Api\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class CreateAnnounceController extends AbstractApiController
{
    #[Route('', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] CreateAnnouncePayload $payload
    ): JsonResponse
    {
        return $this->json($payload);
    }
}
