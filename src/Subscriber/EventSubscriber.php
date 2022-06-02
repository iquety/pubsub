<?php

declare(strict_types=1);

namespace Freep\PubSub\Subscriber;

interface EventSubscriber
{
    public function handleEvent(string $aPayload): void;

    public function subscribedToEventType(): string;
}
