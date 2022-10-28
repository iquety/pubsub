<?php

declare(strict_types=1);

namespace Iquety\PubSub\Event\Serializer;

use RuntimeException;
use Throwable;

class PhpEventSerializer implements EventSerializer
{
    /** @param array<string,mixed> $eventData */
    public function serialize(array $eventData): string
    {
        return serialize($eventData);
    }

    /** @return array<string,mixed> */
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
