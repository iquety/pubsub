<?php

declare(strict_types=1);

namespace Iquety\PubSub\Routine\Events;

use DateTimeImmutable;
use Iquety\PubSub\Event\Event;

class EventTwo extends Event
{
    public function __construct(
        private string $name,
        private int $cpf,
        private DateTimeImmutable $schedule
    ) {
    }

    public static function label(): string
    {
        return 'event-two';
    }

    public function cpf(): int
    {
        return $this->cpf;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function schedule(): DateTimeImmutable
    {
        return $this->schedule;
    }
}
