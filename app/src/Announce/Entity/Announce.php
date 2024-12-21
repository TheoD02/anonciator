<?php

declare(strict_types=1);

namespace App\Announce\Entity;

use App\Announce\Repository\AnnounceRepository;
use App\Resource\Entity\Resource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnounceRepository::class)]
class Announce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AnnounceCategory $category = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8)]
    private ?string $location = null;

    #[ORM\Column(length: 40)]
    private ?string $status = null;

    /**
     * @var Collection<int, resource>
     */
    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'announce')]
    private Collection $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?AnnounceCategory
    {
        return $this->category;
    }

    public function setCategory(?AnnounceCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
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
        if (! $this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setAnnounce($this);
        }

        return $this;
    }

    public function removePhoto(Resource $photo): static
    {
        // set the owning side to null (unless already changed)
        if ($this->photos->removeElement($photo) && $photo->getAnnounce() === $this) {
            $photo->setAnnounce(null);
        }

        return $this;
    }
}
