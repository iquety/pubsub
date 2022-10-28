<?php

declare(strict_types=1);

namespace Iquety\PubSub\Event;

use DateTimeImmutable;

class EventSignal implements Event
{
    /** @param array<string,mixed> $context */
    public function __construct(
        private string $signal = Signals::STOP,
        private array $context = []
    ) {
    }

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        return new self($values['signal'], $values['context']);
    }

    public function label(): string
    {
        return $this->signal;
    }

    public function ocurredOn(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    public function sameEventAs(Event $other): bool
    {
        return $other instanceof EventSignal
            && $this->label() === $other->label();
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return $this->context;
    }
}
