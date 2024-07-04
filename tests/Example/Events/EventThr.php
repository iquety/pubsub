<?php

declare(strict_types=1);

namespace Tests\Example\Events;

use DateTimeImmutable;
use Iquety\PubSub\Event\Event;

class EventThr extends Event
{
    public function __construct(
        private Name $name,
        private Serial $serial,
        private DateTimeImmutable $schedule
    ) {
    }

    public static function label(): string
    {
        return 'event-thr';
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function serial(): Serial
    {
        return $this->serial;
    }

    public function schedule(): DateTimeImmutable
    {
        return $this->schedule;
    }
}
