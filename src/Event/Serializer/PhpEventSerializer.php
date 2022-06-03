<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use Freep\PubSub\Event\Event;
use RuntimeException;
use Throwable;

class PhpEventSerializer extends AbstractEventSerializer
{
    public function serialize(Event $event): string
    {
        return $event::class
            . PHP_EOL
            . serialize($event->toArray());
    }

    public function unserialize(string $serializedEvent): Event
    {
        $className = $this->getEventType($serializedEvent);
        $serialized = $this->getEventSerialized($serializedEvent);

        try {
            $arguments = unserialize($serialized);
        } catch (Throwable $exception) {
            throw new RuntimeException("The serialized PHP object is corrupted: " . $exception->getMessage());
        }

        return $className::factory($arguments);
    }
}
