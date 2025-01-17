<?php

namespace App\Conversation\Builder;

use App\Conversation\Entity\Conversation;
use App\User\Entity\User;

class ConversationBuilder
{
    private Conversation $instance;

    public function __construct()
    {
        $this->instance = new Conversation();
    }

    public static function new(): self
    {
        return new self();
    }

    public function withAnnounce($announce): self
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
