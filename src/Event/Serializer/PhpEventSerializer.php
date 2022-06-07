<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use RuntimeException;
use Throwable;

class PhpEventSerializer implements EventSerializer
{
    public function serialize(array $eventData): string
    {
        return serialize($eventData);
    }

    public function unserialize(string $eventSerializedData): array
    {
        try {
            $eventData = unserialize($eventSerializedData);
        } catch (Throwable $exception) {
            throw new RuntimeException("The serialized PHP object is corrupted: " . $exception->getMessage());
        }

        return $eventData;
    }
}
