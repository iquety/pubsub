<?php

declare(strict_types=1);

namespace Freep\PubSub\Event;

use DateTimeImmutable;

interface Event
{
     /** @param array<string,mixed> $values */
    public static function factory(array $values): Event;

    public function label(): string;

    public function ocurredOn(): DateTimeImmutable;

    public function sameEventAs(Event $other): bool;

    /** @return array<mixed> */
    public function toArray(): array;
}
