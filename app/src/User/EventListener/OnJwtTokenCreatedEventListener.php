<?php

declare(strict_types=1);

namespace App\User\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created', method: 'onJwtTokenCreated')]
class OnJwtTokenCreatedEventListener
{
    public function onJwtTokenCreated(JWTCreatedEvent $event): void
    {
        $data = $event->getData();

        $user = $event->getUser();

        $data['id'] = $user->getId();
        $data['username'] = $user->getUsername();

        $event->setData($data);
    }
}
