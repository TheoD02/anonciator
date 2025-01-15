<?php

declare(strict_types=1);

namespace App\Conversation\Controller;

use App\Conversation\Dto\Payload\SendMessagePayload;
use App\Conversation\Dto\Response\MessageResponse;
use App\Conversation\Service\MessageService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use App\User\Entity\User;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Tag(name: 'Conversation')]
class SendMessageController extends AbstractApiController
{
    #[Route('/{id}/messages', methods: [Request::METHOD_POST])]
    #[SuccessResponse(
        dataFqcn: MessageResponse::class,
        description: 'Conversation created',
        groups: [ApiGroups::POST],
        statusCode: Response::HTTP_CREATED
    )]
    public function __invoke(
        int                                     $id, // Conversation ID
        #[MapRequestPayload] SendMessagePayload $payload,
        MessageService                          $messageService,
        #[CurrentUser] ?User                    $user = null,
    ): Response
    {
        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }

        $message = $messageService->createEntityFromPayload($id, $payload, $user);

        return $this->successResponse(
            data: $message,
            target: MessageResponse::class,
            groups: [ApiGroups::POST],
            status: Response::HTTP_CREATED
        );
    }
}
