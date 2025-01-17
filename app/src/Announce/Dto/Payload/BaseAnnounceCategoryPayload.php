<?php

declare(strict_types=1);

namespace App\Announce\Dto\Payload;

use App\Shared\PayloadInterface;
use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints as Assert;

class BaseAnnounceCategoryPayload implements PayloadInterface
{
    #[Property(description: 'Category name', example: 'Cars')]
    #[Assert\NotBlank()]
    public string $name;
}
