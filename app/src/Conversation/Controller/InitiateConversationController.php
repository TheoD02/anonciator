<?php

declare(strict_types=1);

namespace App\Conversation\Controller;

use App\Conversation\Dto\Response\ConversationResponse;
use App\Conversation\Enum\ApiGroups;
use App\Conversation\Service\ConversationService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use App\User\Entity\User;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Tag('Conversation')]
class InitiateConversationController extends AbstractApiController
{
    #[Route('/initiate/{announceId}', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: ConversationResponse::class,
        description: 'Conversation initiated',
        groups: [ApiGroups::INIT],
        statusCode: Response::HTTP_OK,
    )]
    public function __invoke(
        int                  $announceId,
        ConversationService  $conversationService,
        #[CurrentUser] ?User $user = null,
    ): Response
    {
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

        $conversation = $conversationService->initConversationOrGetExisting($announceId, $user);

        return $this->successResponse(
            data: $conversation,
            target: ConversationResponse::class,
            groups: [ApiGroups::INIT],
            status: Response::HTTP_OK,
        );
    }
}
