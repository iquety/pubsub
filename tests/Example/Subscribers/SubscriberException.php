<?php

declare(strict_types=1);

namespace Tests\Example\Subscribers;

use Exception;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Subscriber\EventSubscriber;

class SubscriberException implements EventSubscriber
{
    public function handleEvent(Event $event): void
    {
        throw new Exception('Exception in subscriber handle to event ' . $event::class);
    }

    public function subscribedToEventType(): string
    {
        // todos os tipos de eventos serão recebidos por este assinante
        return Event::class;
    }
}
