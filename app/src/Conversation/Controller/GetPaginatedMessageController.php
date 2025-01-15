<?php

declare(strict_types=1);

namespace App\Conversation\Controller;

use App\Conversation\Dto\Filter\PaginateMessageFilter;
use App\Conversation\Dto\Response\MessageResponse;
use App\Conversation\Service\MessageService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Conversation')]
class GetPaginatedMessageController extends AbstractApiController
{
    #[Route('/{id}/messages', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: MessageResponse::class,
        description: 'Get paginated messages',
        groups: [ApiGroups::GET_PAGINATED],
        statusCode: Response::HTTP_OK
    )]
    public function __invoke(int $id, MessageService $messageService): Response
    {
        $filter = new PaginateMessageFilter();
        $filter->announceId = $id; // This is conversation ID not announce ID (TODO: Fix this)
        $messages = $messageService->paginateEntities($filter);

        return $this->successResponse(
            data: $messages,
            target: MessageResponse::class,
            groups: [ApiGroups::GET_PAGINATED],
            status: Response::HTTP_OK
        );
    }
}
