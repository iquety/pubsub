<?php

declare(strict_types=1);

namespace Tests\Example\Subscribers;

use DateTimeImmutable;
use DateTimeZone;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Subscriber\EventSubscriber;
use Iquety\Security\Filesystem;
use Tests\Example\Events\EventOne;
use Tests\Example\Events\EventTwo;

/** @SuppressWarnings(PHPMD.StaticAccess) */
class SubscriberTwo implements EventSubscriber
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
        $file = new Filesystem(dirname(__DIR__, 2) . '/files');

        $file->setFileContents(
            'subscriber-two-handle.txt',
            __CLASS__ . PHP_EOL .
            'recebeu: ' . $event::class . PHP_EOL .
            'em: ' . $event->ocurredOn()->format('Y-m-d H:i:s')
        );
    }

    public function receiveInTimezone(): DateTimeZone
    {
        return new DateTimeZone('America/New_York');
    }
}
