<?php

declare(strict_types=1);

namespace App\Conversation\Builder;

use App\Announce\Entity\Announce;
use App\Conversation\Entity\Conversation;
use App\User\Entity\User;

class ConversationBuilder
{
    private readonly Conversation $instance;

    public function __construct()
    {
        $this->instance = new Conversation();
    }

    public static function new(): self
    {
        return new self();
    }

    public function withAnnounce(?Announce $announce): self
    {
        $this->instance->setAnnounce($announce);
        $this->withName($announce->getTitle());

        return $this;
    }

    public function withName(string $name): self
    {
        $this->instance->setName($name);

        return $this;
    }

    public function build(): Conversation
    {
        return $this->instance;
    }

    public function withInitializedBy(User $loggedUser): self
    {
        $this->instance->setInitializedBy($loggedUser);

        return $this;
    }

    public function withReceiver(User $announceCreator): self
    {
        $this->instance->setReceiver($announceCreator);

        return $this;
    }
}
