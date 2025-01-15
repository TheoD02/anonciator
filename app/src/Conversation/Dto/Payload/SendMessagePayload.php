<?php

declare(strict_types=1);

namespace App\Conversation\Dto\Payload;

use App\Shared\PayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SendMessagePayload implements PayloadInterface
{
    #[Assert\NotBlank]
    public string $content;
}
