<?php

declare(strict_types=1);

namespace Tests\Event\Support;

use DateTime;
use Iquety\PubSub\Event\Event;

class EventNoConstructor extends Event
{
    public function label(): string
    {
        return 'post.register.v1';
    }
}
