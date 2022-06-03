<?php

declare(strict_types=1);

namespace Freep\PubSub\Subscriber;

use Freep\PubSub\Event\Event;

interface EventSubscriber
{
    public function handleEvent(Event $event): void;

    public function subscribedToEventType(): string;
}
