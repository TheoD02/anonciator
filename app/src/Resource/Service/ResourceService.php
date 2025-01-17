<?php

declare(strict_types=1);

namespace App\Resource\Service;

use App\Resource\Entity\Resource;
use App\Shared\Trait\EntityCrudServiceTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @template T of Resource
 *
 * @extends EntityCrudServiceTrait<T>
 */
class ResourceService
{
    use EntityCrudServiceTrait {
        createEntity as _createEntity;
    }

    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        private readonly string $projectDir,
    ) {
    }

    /**
     * @return array<resource>
     */
    public function createManyResources(UploadedFile ...$files): array
    {
        $resources = [];

        foreach ($files as $file) {
            $resources[] = $this->createResource($file, false);
        }

        $this->em->flush();

        return $resources;
    }

    public function createResource(UploadedFile $file, bool $flush = true): Resource
    {
        $resource = new Resource();

        $destination = "{$this->projectDir}/public/uploads";
        $relativeDestination = str_replace("{$this->projectDir}/public", '', $destination);

        $filename = \sprintf('%s.%s', md5(uniqid()), $file->getClientOriginalExtension());

        $resource->setPath("{$relativeDestination}/{$filename}");
        $resource->setBucket('localfs');
        $resource->setOriginalName($file->getClientOriginalName());

        $file->move($destination, $filename);

        return $this->_createEntity($resource, $flush);
    }

    public function deleteEntityFromId(int $id): void
    {
        /** @var resource $resource */
        $resource = $this->getEntityById($id, fail: true);

        new Filesystem()->remove("{$this->projectDir}/public{$resource->getPath()}");

        $this->deleteEntity($resource);
    }

    protected function getEntityClass(): string
    {
        return Resource::class;
    }
}
