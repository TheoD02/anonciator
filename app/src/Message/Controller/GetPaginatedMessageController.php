<?php

namespace App\Message\Controller;

use App\Message\Dto\Filter\PaginateMessageFilter;
use App\Message\Dto\Response\MessageResponse;
use App\Message\Service\MessageService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\ApiGroups;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Message')]
class GetPaginatedMessageController extends AbstractApiController
{
    #[Route('/{announceId}', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: MessageResponse::class,
        description: 'Get paginated messages',
        groups: [ApiGroups::GET_PAGINATED],
        statusCode: Response::HTTP_OK
    )]
    public function __invoke(
        int $announceId,
        MessageService $messageService,
    ): Response
    {
        $filter = new PaginateMessageFilter();
        $filter->announceId = $announceId;
        $messages = $messageService->paginateEntities($filter);

        return $this->successResponse(
            data: $messages,
            target: MessageResponse::class,
            groups: [ApiGroups::GET_PAGINATED],
            status: Response::HTTP_OK
        );
    }
}
