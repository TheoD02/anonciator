<?php

declare(strict_types=1);

namespace App\Conversation\Controller;

use App\Conversation\Dto\Response\MessageResponse;
use App\Conversation\Service\MessageService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use App\Shared\Api\PaginationFilterQuery;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Conversation')]
class GetPaginatedConversationMessagesController extends AbstractApiController
{
    #[Route('/{id}/messages', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: MessageResponse::class,
        description: 'Get paginated conversation messages',
        groups: [GlobalApiGroups::GET_PAGINATED],
        statusCode: Response::HTTP_OK
    )]
    public function __invoke(
        int $id,
        MessageService $service,
        #[MapQueryString] PaginationFilterQuery $paginationFilterQuery,
    ): Response {
        $conversation = $service->getMessagesForConversation($id, $paginationFilterQuery);

        return $this->successResponse(
            data: $conversation,
            target: MessageResponse::class,
            groups: [GlobalApiGroups::GET_PAGINATED],
            status: Response::HTTP_OK
        );
    }
}
