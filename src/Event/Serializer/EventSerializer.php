<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use Freep\PubSub\Event\Event;

interface EventSerializer
{
    public function serialize(array $eventData): string;

    public function unserialize(string $eventSerializedData): array;
}
