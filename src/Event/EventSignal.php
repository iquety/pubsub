<?php

declare(strict_types=1);

namespace Freep\PubSub\Event;

use DateTimeImmutable;

class EventSignal implements Event
{
    public function __construct(private string $signal = Signals::STOP)
    {
    }

    public function signal(): string
    {
        return $this->signal;
    }

    public function ocurredOn(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    public function sameEventAs(Event $other): bool
    {
        return $other instanceof EventSignal && $this->signal() === $other->signal();
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [];
    }
}
