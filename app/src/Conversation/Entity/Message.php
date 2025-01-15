<?php

declare(strict_types=1);

namespace App\Conversation\Entity;

use App\Conversation\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private string $sentBy = '';

    #[ORM\Column]
    private string $sentTo = '';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $wasReadAt = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conversation $conversation = null;

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

    public function getSentBy(): string
    {
        return $this->sentBy;
    }

    public function setSentBy(string|UserInterface $sentBy): static
    {
        $this->sentBy = \is_string($sentBy) ? $sentBy : $sentBy->getUserIdentifier();

        return $this;
    }

    public function getSentTo(): string
    {
        return $this->sentTo;
    }

    public function setSentTo(string|UserInterface $sentTo): static
    {
        $this->sentTo = \is_string($sentTo) ? $sentTo : $sentTo->getUserIdentifier();

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

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }
}
