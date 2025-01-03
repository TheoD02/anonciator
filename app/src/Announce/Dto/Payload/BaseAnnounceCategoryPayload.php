<?php

declare(strict_types=1);

namespace App\Announce\Dto\Payload;

use App\Shared\PayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BaseAnnounceCategoryPayload implements PayloadInterface
{
    #[Assert\NotBlank()]
    public string $name;
}
