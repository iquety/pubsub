<?php

declare(strict_types=1);

namespace Tests\Example\Subscribers;

use DateTimeImmutable;
use Exception;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Subscriber\EventSubscriber;
use Tests\Example\Events\EventOne;
use Tests\Example\Events\EventTwo;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class SubscriberException implements EventSubscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        if ($eventLabel === 'event-one') {
            return EventOne::factory($eventData);
        }

        if ($eventLabel === 'event-two') {
            return EventTwo::factory($eventData);
        }

        return null;
    }

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
