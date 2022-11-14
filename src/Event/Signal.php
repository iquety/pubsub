<?php

declare(strict_types=1);

namespace Iquety\PubSub\Event;

abstract class Signal extends Event
{
    public const STOP = 'signal.stop';

    /** @param array<string,mixed> $context */
    public function __construct(private array $context = [])
    {
    }

    public function sameEventAs(Event $other): bool
    {
        return $other instanceof Signal
            && $this::label() === $other::label();
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return $this->context;
    }
}
