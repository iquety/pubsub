<?php

declare(strict_types=1);

namespace Tests\Event\Support;

use Iquety\PubSub\Event\Event;

class EventArrayIncorrect extends Event
{
    public function __construct(
        private string $title
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

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        $array = parent::toArray();

        unset($array['occurredOn']);

        return $array;
    }
}
