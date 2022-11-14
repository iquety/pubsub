<?php

declare(strict_types=1);

namespace Tests\Event\Support;

use DateTimeImmutable;
use Iquety\PubSub\Event\Event;

class EventOccurred extends Event
{
    public function __construct(
        private string $title,
        private string $description,
        private DateTimeImmutable $schedule
    ) {
    }

    public static function label(): string
    {
        return 'post.register.v1';
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function schedule(): DateTimeImmutable
    {
        return $this->schedule;
    }
}
