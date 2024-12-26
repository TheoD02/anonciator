<?php

declare(strict_types=1);

namespace App\Announce\Dto\Payload;

use App\Announce\AnnounceStatus;
use App\Shared\Api\MapRelation;
use App\Shared\Api\Relation;
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
    #[Assert\Valid]
    #[Assert\NotBlank]
    #[MapRelation(toProperty: 'category', many: false)]
    public Relation $category;

    #[Property(description: 'Location of the announce', example: '41.40338, 2.17403')]
    #[Assert\NotBlank()]
    public string $location;

    #[Property(description: 'Status of the announce', example: 'draft')]
    #[Assert\NotBlank()]
    public AnnounceStatus $status;

    #[Property(description: 'Photo IDs of the announce', example: '[1, 2, 3]')]
    #[Assert\Valid]
    #[MapRelation(toProperty: 'photos', many: true, allowEmpty: true)]
    public ?Relation $photos = null;
}
