<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use Freep\PubSub\Event\Event;

interface EventSerializer
{
    /** @param array<string,mixed> $eventData */
    public function serialize(array $eventData): string;

    /** @return array<string,mixed> */
    public function unserialize(string $eventSerializedData): array;
}
