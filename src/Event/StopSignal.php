<?php

declare(strict_types=1);

namespace Iquety\PubSub\Event;

class StopSignal extends Signal
{
    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        return new self($values['context']);
    }

    public static function label(): string
    {
        return self::STOP;
    }
}
