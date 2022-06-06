<?php

declare(strict_types=1);

namespace Tests\Example\Subscribers;

use Freep\PubSub\Event\Event;
use Freep\PubSub\Subscriber\EventSubscriber;

class SubscriberError implements EventSubscriber
{
    public function handleEvent(Event $event): void
    {
        trigger_error(
            'Error triggered in subscriber handle to event ' . $event::class,
            E_USER_ERROR
        );
    }

    public function subscribedToEventType(): string
    {
        // todos os tipos de eventos serão recebidos por este assinante
        return Event::class;
    }
}
