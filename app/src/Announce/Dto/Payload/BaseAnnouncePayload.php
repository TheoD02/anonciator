<?php

namespace App\Announce\Dto\Payload;

use App\Announce\AnnounceStatus;
use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints as Assert;

class BaseAnnouncePayload
{
    #[Property(description: 'Title of the announce', example: 'This is a title')]
    #[Assert\NotBlank()]
    public string $title;

    #[Property(description: 'Description of the announce', example: 'This is a description')]
    #[Assert\NotBlank()]
    public string $description;

    #[Property(description: 'Price of the announce', example: '100.00')]
    #[Assert\Positive()]
    #[Assert\NotBlank()]
    public string $price;

    #[Property(description: 'Category ID of the announce', example: '1')]
    #[Assert\NotBlank()]
    #[Assert\Positive()]
    public int $categoryId;

    #[Property(description: 'Location of the announce', example: '41.40338, 2.17403')]
    #[Assert\NotBlank()]
    public string $location;

    #[Property(description: 'Status of the announce', example: 'draft')]
    #[Assert\NotBlank()]
    public AnnounceStatus $status;

    #[Property(description: 'Photo IDs of the announce', example: '[1, 2, 3]')]
    #[Assert\NotNull()]
    public array $photoIds;
}
