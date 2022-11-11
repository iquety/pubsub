<?php

declare(strict_types=1);

namespace Iquety\PubSub\Subscriber;

use DateTimeZone;
use Iquety\PubSub\Event\Event;

interface EventSubscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event;

    public function handleEvent(Event $event): void;
}
