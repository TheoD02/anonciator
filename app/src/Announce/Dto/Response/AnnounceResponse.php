<?php

declare(strict_types=1);

namespace App\Announce\Dto\Response;

use App\Announce\AnnounceStatus;
use App\Announce\Entity\Announce;
use App\Resource\Entity\Resource;
use App\Shared\Api\GlobalApiGroups;
use App\Shared\Api\Visibility;
use AutoMapper\Attribute\MapFrom;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Attribute\Groups;

class AnnounceResponse
{
    #[Property(description: 'ID of the announce', example: '1')]
    #[Groups([
        GlobalApiGroups::POST,
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    public int $id;

    #[Property(description: 'Title of the announce', example: 'This is a title')]
    #[Groups([
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    public string $title;

    #[Property(description: 'Description of the announce', example: 'This is a description')]
    #[Groups([
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    public string $description;

    #[Property(description: 'Price of the announce', example: '100.00')]
    #[Groups([
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    #[Visibility(external: false, internal: true)]
    public string $price;

    #[Property(description: 'Category ID of the announce', example: '1')]
    #[Groups([
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    #[MapFrom(transformer: 'source.getCategory().getId()')]
    public int $categoryId;

    #[Property(description: 'Location of the announce', example: '41.0987')]
    #[Groups([
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    public string $location;

    #[Property(description: 'Status of the announce', example: 'draft')]
    #[Groups([
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    public AnnounceStatus $status;

    #[Property(description: 'Photo IDs of the announce', example: [1, 2, 3])]
    #[Groups([
        GlobalApiGroups::GET_PAGINATED,
        GlobalApiGroups::GET_ONE,
        GlobalApiGroups::PUT,
        GlobalApiGroups::DELETE,
        GlobalApiGroups::PATCH,
    ])]
    #[MapFrom(transformer: [self::class, 'mapPhotoIds'])]
    public array $photoIds;

    #[Property(description: 'Created at', example: '2021-01-01T00:00:00+00:00')]
    #[Groups([GlobalApiGroups::GET_ONE])]
    public \DateTimeImmutable $createdAt;

    #[Property(description: 'Created by', example: 'admin@domain.tld')]
    #[Groups([GlobalApiGroups::GET_ONE])]
    public string $createdBy;

    public static function mapPhotoIds(mixed $value, Announce $source, array $context): array
    {
        return $source->getPhotos()->map(static fn (Resource $photo): ?int => $photo->getId())->toArray();
    }
}
