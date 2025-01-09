<?php

declare(strict_types=1);

namespace App\Message\Controller;

use App\Message\Dto\Payload\SendMessagePayload;
use App\Message\Dto\Response\MessageResponse;
use App\Message\Service\MessageService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Message')]
class SendMessageController extends AbstractApiController
{
    #[Route('', methods: [Request::METHOD_POST])]
    #[SuccessResponse(
        dataFqcn: MessageResponse::class,
        description: 'Message created',
        groups: [ApiGroups::POST],
        statusCode: Response::HTTP_CREATED
    )]
    public function __invoke(
        #[MapRequestPayload] SendMessagePayload $payload,
        MessageService $messageService,
    ): Response {
        $message = $messageService->createEntityFromPayload($payload);

        return $this->successResponse(
            data: $message,
            target: MessageResponse::class,
            groups: [ApiGroups::POST],
            status: Response::HTTP_CREATED
        );
    }
}
