<?php

namespace App\Tests\Announce\Controller\Announce;

use App\Announce\Controller\Announce\DeleteAnnounceController;
use App\Tests\AbstractApiWebTestCase;

class DeleteAnnounceControllerTest extends AbstractApiWebTestCase
{
    public function getAction(): string
    {
        return DeleteAnnounceController::class;
    }

    public function expectedUrl(): string
    {
        return '/api/announces/{id}';
    }
}
