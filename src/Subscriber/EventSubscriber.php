<?php

declare(strict_types=1);

namespace Freep\PubSub\Subscriber;

use DateTimeZone;
use Freep\PubSub\Event\Event;

interface EventSubscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event;

    public function handleEvent(Event $event): void;

    public function receiveInTimezone(): DateTimeZone;
}
