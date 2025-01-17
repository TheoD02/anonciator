<?php

declare(strict_types=1);

namespace App\Tests\Announce\Controller\Announce;

use App\Announce\Controller\Announce\DeleteAnnounceController;
use App\Tests\AbstractApiWebTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(DeleteAnnounceController::class)]
final class DeleteAnnounceControllerTest extends AbstractApiWebTestCase
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
