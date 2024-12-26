<?php

declare(strict_types=1);

namespace App\Announce\Service;

use App\Announce\Entity\Announce;
use App\Shared\Exception\AbstractDomainModelNotFoundException;

class AnnounceNotFoundException extends AbstractDomainModelNotFoundException
{
    public function model(): string
    {
        return Announce::class;
    }
}
