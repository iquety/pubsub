<?php

declare(strict_types=1);

namespace Tests\Event\Support;

use DateTimeImmutable;
use Iquety\PubSub\Event\Event;

class EventFactoryIncorrect extends Event
{
    public function __construct(
        private string $title,
        private DateTimeImmutable $date
    ) {
    }

    public function label(): string
    {
        return 'post.register.v1';
    }

    public function title(): string
    {
        return $this->title;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        $className = get_called_class();

        return new $className(...$values);
    }
}
