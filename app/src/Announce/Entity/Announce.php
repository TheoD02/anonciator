<?php

declare(strict_types=1);

namespace App\Announce\Entity;

use App\Announce\Enum\AnnounceStatus;
use App\Announce\Repository\AnnounceRepository;
use App\Resource\Entity\Resource;
use App\Shared\Api\Visibility;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: AnnounceRepository::class)]
class Announce
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $description = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Visibility(external: false)]
    private string $price = '0.00';

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AnnounceCategory $category;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8)]
    private string $location = '00.00000000';

    #[ORM\Column(length: 40, enumType: AnnounceStatus::class)]
    private AnnounceStatus $status = AnnounceStatus::DRAFT;

    /**
     * @var Collection<int, resource>
     */
    #[ORM\ManyToMany(targetEntity: Resource::class)]
    #[ORM\JoinTable(
        name: 'announce_photos',
        joinColumns: [
            new ORM\JoinColumn(
                name: 'announce_id',
                referencedColumnName: 'id',
                nullable: false,
                onDelete: 'CASCADE',
            ),
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(
                name: 'resource_id',
                referencedColumnName: 'id',
                nullable: false,
                onDelete: 'CASCADE',
            ),
        ],
    )]
    private Collection $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): AnnounceCategory
    {
        return $this->category;
    }

    public function setCategory(AnnounceCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getStatus(): AnnounceStatus
    {
        return $this->status;
    }

    public function setStatus(AnnounceStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, resource>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Resource $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
        }

        return $this;
    }

    public function removePhoto(Resource $photo): static
    {
        $this->photos->removeElement($photo);

        return $this;
    }
}
