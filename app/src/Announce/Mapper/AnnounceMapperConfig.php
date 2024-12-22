<?php

declare(strict_types=1);

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

class AnnounceMapperConfig implements AutoMapperConfiguratorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function configure(AutoMapperConfigInterface $config): void
    {
        $this->announceEntityToAnnounceResponse($config);
        $this->createAnnouncePayloadToAnnounceEntity($config);
    }

    public function announceEntityToAnnounceResponse(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(Announce::class, AnnounceResponse::class)
            ->forMember('categoryId', static fn (Announce $source): ?int => $source->getCategory()->getId())
            ->forMember(
                'photoIds',
                static fn (Announce $source) => $source->getPhotos()->map(
                    static fn (Resource $photo): ?int => $photo->getId()
                )->toArray()
            )
        ;
    }

    private function createAnnouncePayloadToAnnounceEntity(AutoMapperConfigInterface $config): void
    {
        $config->registerMapping(CreateAnnouncePayload::class, Announce::class)
            ->forMember(
                'category',
                fn (CreateAnnouncePayload $source): ?object => $this->em->getReference(
                    AnnounceCategory::class,
                    $source->categoryId
                )
            )
            ->forMember(
                'photos',
                fn (CreateAnnouncePayload $source): ArrayCollection => new ArrayCollection(\array_map(
                    fn (int $photoId): ?object => $this->em->getReference(Resource::class, $photoId),
                    $source->photoIds
                ))
            )
        ;
    }
}
