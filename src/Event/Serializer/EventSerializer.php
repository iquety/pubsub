<?php

declare(strict_types=1);

namespace Iquety\PubSub\Event\Serializer;

use Iquety\PubSub\Event\Event;

interface EventSerializer
{
    /** @param array<string,mixed> $eventData */
    public function serialize(array $eventData): string;

    /** @return array<string,mixed> */
    public function unserialize(string $eventSerializedData): array;
}
