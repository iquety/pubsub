<?php

declare(strict_types=1);

namespace Tests\Example\Subscribers;

use DateTimeImmutable;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Subscriber\EventSubscriber;
use Tests\Example\Events\EventOne;
use Freep\Security\Filesystem;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class SubscriberOne implements EventSubscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        if ($eventLabel === 'event-one') {
            return EventOne::factory($eventData);
        }

        return null;
    }

    public function handleEvent(Event $event): void
    {
        $file = new Filesystem(dirname(__DIR__, 2) . '/files');

        $file->setFileContents(
            'subscriber-one-handle.txt',
            __CLASS__ . ' recebeu ' . PHP_EOL . $event::class
        );
    }

    public function subscribedToEventType(): string
    {
        // Apenas eventos deste tipo serão recebidos por este assinante
        return EventOne::class;
    }
}
