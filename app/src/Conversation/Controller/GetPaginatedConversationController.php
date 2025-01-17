<?php

declare(strict_types=1);

namespace App\Conversation\Controller;

use App\Conversation\Dto\Response\ConversationResponse;
use App\Conversation\Service\ConversationService;
use App\Shared\Api\AbstractApiController;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Nelmio\Attribute\ErrorResponse;
use App\Shared\Api\Nelmio\Attribute\SuccessResponse;
use App\Shared\Api\PaginationFilterQuery;
use OpenApi\Attributes\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Tag(name: 'Conversation')]
class GetPaginatedConversationController extends AbstractApiController
{
    #[Route('', methods: [Request::METHOD_GET])]
    #[SuccessResponse(
        dataFqcn: ConversationResponse::class,
        description: 'Get paginated conversation',
        groups: [GlobalApiGroups::GET_PAGINATED],
        statusCode: Response::HTTP_OK
    )]
    #[ErrorResponse(statusCode: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized')]
    #[ErrorResponse(statusCode: Response::HTTP_FORBIDDEN, description: 'Forbidden')]
    #[ErrorResponse(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid input')]
    #[ErrorResponse(statusCode: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error')]
    public function __invoke(
        ConversationService                     $service,
        #[MapQueryString] PaginationFilterQuery $paginationFilterQuery,
    ): Response
    {
        $conversations = $service->paginateEntities(paginationFilterQuery: $paginationFilterQuery);

        return $this->successResponse(
            data: $conversations,
            target: ConversationResponse::class,
            groups: [GlobalApiGroups::GET_PAGINATED],
            status: Response::HTTP_OK
        );
    }
}
