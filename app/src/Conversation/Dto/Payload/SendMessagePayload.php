<?php

declare(strict_types=1);

namespace App\Conversation\Dto\Payload;

use App\Shared\PayloadInterface;
use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints as Assert;

class SendMessagePayload implements PayloadInterface
{
    #[Property(description: 'Message content', example: 'Hello, how are you?')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $content;
}
