<?php

declare(strict_types=1);

namespace Tests\Example\Subscribers;

use DateTimeImmutable;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Subscriber\EventSubscriber;
use Freep\Security\Filesystem;
use Tests\Example\Events\EventOne;
use Tests\Example\Events\EventTwo;

class SubscriberTwo implements EventSubscriber
{
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        if ($eventLabel === 'event-one') {
            return new EventOne(
                $eventData['name'],
                $eventData['cpf'],
                new DateTimeImmutable($eventData['ocurredOn']),
            );
        }

        if ($eventLabel === 'event-two') {
            return new EventTwo(
                $eventData['name'],
                $eventData['cpf'],
                new DateTimeImmutable($eventData['ocurredOn']),
            );
        }

        return null;
    }

    public function handleEvent(Event $event): void
    {
        $file = new Filesystem(dirname(__DIR__, 2) . '/files');

        $file->setFileContents(
            'subscriber-two-handle.txt',
            __CLASS__ . ' recebeu ' . PHP_EOL . $event::class
        );
    }

    public function subscribedToEventType(): string
    {
        // todos os tipos de eventos serão recebidos por este assinante
        return Event::class;
    }
}
