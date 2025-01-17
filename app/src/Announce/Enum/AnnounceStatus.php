<?php

declare(strict_types=1);

namespace App\Announce\Enum;

enum AnnounceStatus: string
{
    case PUBLISHED = 'published';
    case DRAFT = 'draft';
    case ARCHIVED = 'archived';
    case DELETED = 'deleted';
}
