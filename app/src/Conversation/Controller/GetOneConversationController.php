<?php

declare(strict_types=1);

namespace App\Conversation\Controller;

use App\Conversation\Dto\Filter\PaginateMessageFilter;
use App\Conversation\Dto\Response\ConversationResponse;
use App\Conversation\Service\ConversationService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Conversation')]
class GetOneConversationController extends AbstractApiController
{
    #[Route('/{id}', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: ConversationResponse::class,
        description: 'Get one conversation',
        groups: [GlobalApiGroups::GET_ONE],
        statusCode: Response::HTTP_OK
    )]
    public function __invoke(int $id, ConversationService $service): Response
    {
        $conversation = $service->getEntityById($id);

        return $this->successResponse(
            data: $conversation,
            target: ConversationResponse::class,
            groups: [GlobalApiGroups::GET_ONE],
            status: Response::HTTP_OK
        );
    }
}
