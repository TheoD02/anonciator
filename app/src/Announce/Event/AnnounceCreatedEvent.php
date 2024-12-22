<?php

declare(strict_types=1);

namespace App\Announce\Event;

use App\Announce\Entity\Announce;

class AnnounceCreatedEvent
{
    public function __construct(
        public readonly Announce $announce,
    ) {
    }
}
