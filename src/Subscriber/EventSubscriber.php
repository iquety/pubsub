<?php

declare(strict_types=1);

namespace Freep\PubSub\Subscriber;

use Freep\PubSub\Event\Event;

interface EventSubscriber
{
    public function eventFactory(string $eventLabel, array $eventData): ?Event;

    public function handleEvent(Event $event): void;
}
