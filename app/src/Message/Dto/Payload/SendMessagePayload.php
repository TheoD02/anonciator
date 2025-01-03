<?php

namespace App\Message\Dto\Payload;

use App\Shared\PayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SendMessagePayload implements PayloadInterface
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $announceId;

    #[Assert\NotBlank]
    public string $content;
}
