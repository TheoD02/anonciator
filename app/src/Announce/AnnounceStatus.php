<?php

namespace App\Announce;

enum AnnounceStatus: string
{
    case PUBLISHED = 'published';
    case DRAFT = 'draft';
    case ARCHIVED = 'archived';
    case DELETED = 'deleted';
}
