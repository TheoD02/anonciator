<?php

declare(strict_types=1);

namespace App\Announce;

use App\Announce\Dto\Payload\CreateAnnouncePayload;
use App\Announce\Entity\Announce;
use App\Announce\Entity\AnnounceCategory;
use App\Resource\Entity\Resource;
use Doctrine\ORM\EntityManagerInterface;

class AnnounceFactory
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function fromCreatePayload(CreateAnnouncePayload $payload): Announce
    {
        $announce = new Announce();
        $announce->setTitle($payload->title);
        $announce->setDescription($payload->description);
        $announce->setPrice($payload->price);
        $announce->setCategory($this->em->getReference(AnnounceCategory::class, $payload->categoryId));
        $announce->setLocation($payload->location);
        $announce->setStatus($payload->status);

        foreach ($payload->photoIds as $photoId) {
            $announce->addPhoto($this->em->getReference(Resource::class, $photoId));
        }

        return $announce;
    }
}
