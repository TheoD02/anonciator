<?php

namespace App\Announce\Mapper;

use App\Announce\Dto\Payload\CreateAnnouncePayload;
use App\Announce\Dto\Response\AnnounceResponse;
use App\Announce\Entity\Announce;
use App\Announce\Entity\AnnounceCategory;
use App\Resource\Entity\Resource;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use function array_map;

class AnnounceMapperConfig implements AutoMapperConfiguratorInterface
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function configure(AutoMapperConfigInterface $config): void
    {
        $this->announceEntityToAnnounceResponse($config);
        $this->createAnnouncePayloadToAnnounceEntity($config);
    }

    public function announceEntityToAnnounceResponse(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(Announce::class, AnnounceResponse::class)
            ->forMember('categoryId', function (Announce $source) {
                return $source->getCategory()->getId();
            })
            ->forMember('photoIds', function (Announce $source) {
                return $source->getPhotos()->map(fn(Resource $photo) => $photo->getId())->toArray();
            });

    }

    private function createAnnouncePayloadToAnnounceEntity(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(CreateAnnouncePayload::class, Announce::class)
            ->forMember('category', function (CreateAnnouncePayload $source) {
                return $this->em->getReference(AnnounceCategory::class, $source->categoryId);
            })
            ->forMember('photos', function (CreateAnnouncePayload $source) {
                return new ArrayCollection(array_map(fn(int $photoId) => $this->em->getReference(Resource::class, $photoId), $source->photoIds));
            });
    }
}
