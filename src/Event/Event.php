<?php

declare(strict_types=1);

namespace Freep\PubSub\Event;

use DateTimeImmutable;

interface Event
{
    public function ocurredOn(): DateTimeImmutable;

    public function sameEventAs(Event $other): bool;

    /** @return array<mixed> */
    public function toArray(): array;
}
