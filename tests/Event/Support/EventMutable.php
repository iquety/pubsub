<?php

declare(strict_types=1);

namespace Tests\Event\Support;

use DateTime;
use Iquety\PubSub\Event\Event;

class EventMutable extends Event
{
    public function __construct(
        private string $title,
        private DateTime $schedule // mutavel
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

    public function schedule(): DateTime
    {
        return $this->schedule;
    }
}
