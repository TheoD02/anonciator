<?php

declare(strict_types=1);

namespace App\Conversation\Dto\Payload;

use App\Shared\PayloadInterface;
use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints as Assert;

class SendMessagePayload implements PayloadInterface
{
    #[Property(description: 'Message content')]
    #[Assert\NotBlank]
    public string $content;
}
