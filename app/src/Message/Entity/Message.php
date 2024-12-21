<?php

declare(strict_types=1);

namespace App\Message\Entity;

use App\Message\Repository\MessageRepository;
use App\User\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sendedBy = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sendedTo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $wasReadAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSendedBy(): ?User
    {
        return $this->sendedBy;
    }

    public function setSendedBy(?User $sendedBy): static
    {
        $this->sendedBy = $sendedBy;

        return $this;
    }

    public function getSendedTo(): ?User
    {
        return $this->sendedTo;
    }

    public function setSendedTo(?User $sendedTo): static
    {
        $this->sendedTo = $sendedTo;

        return $this;
    }

    public function getWasReadAt(): ?\DateTimeImmutable
    {
        return $this->wasReadAt;
    }

    public function setWasReadAt(\DateTimeImmutable $wasReadAt): static
    {
        $this->wasReadAt = $wasReadAt;

        return $this;
    }
}
