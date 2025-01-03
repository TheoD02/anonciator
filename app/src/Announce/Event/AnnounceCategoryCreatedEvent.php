<?php

declare(strict_types=1);

namespace App\Announce\Event;

use App\Announce\Entity\AnnounceCategory;

readonly class AnnounceCategoryCreatedEvent
{
    public function __construct(
        public AnnounceCategory $category,
    ) {
    }
}
