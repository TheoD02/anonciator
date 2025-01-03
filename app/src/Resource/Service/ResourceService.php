<?php

namespace App\Resource\Service;

use App\Announce\Service\EntityCrudServiceTrait;
use App\Resource\Entity\Resource;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function sprintf;

class ResourceService
{
    use EntityCrudServiceTrait {
        createEntity as _createEntity;
    }

    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        private readonly string $projectDir,
    )
    {
    }

    /**
     * @return array<Resource>
     */
    public function createManyResources(UploadedFile ...$files): array
    {
        $resources = [];

        foreach ($files as $file) {
            $resources[] = $this->createEntity($file, false);
        }

        $this->em->flush();

        return $resources;
    }

    public function createEntity(UploadedFile $file, bool $flush = true): Resource
    {
        $resource = new Resource();

        $destination = "$this->projectDir/public/uploads";
        $relativeDestination = str_replace("$this->projectDir/public", '', $destination);

        $filename = sprintf('%s.%s', md5(uniqid()), $file->getClientOriginalExtension());

        $resource->setPath("$relativeDestination/{$filename}");
        $resource->setBucket('localfs');

        $file->move($destination, $filename);

        return $this->_createEntity($resource, $flush);
    }

    protected function getEntityClass(): string
    {
        return Resource::class;
    }
}
