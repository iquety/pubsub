<?php

declare(strict_types=1);

namespace Iquety\PubSub\Routine\Subscribers;

use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Routine\Events\EventOne;
use Iquety\PubSub\Subscriber\EventSubscriber;
use Iquety\Security\Filesystem;

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
        $file = new Filesystem((string)getcwd());

        $file->appendFileContents(
            'subscriber-one-receive.txt',
            __CLASS__ . PHP_EOL .
            'recebeu: ' . $event::class . PHP_EOL .
            'em: ' . $event->occurredOn()->format('Y-m-d H:i:s') . PHP_EOL
        );
    }
}
