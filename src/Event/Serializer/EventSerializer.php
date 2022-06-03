<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use Freep\PubSub\Event\Event;

interface EventSerializer
{
    public function getEventType(string $serializedEvent): string;

    public function serialize(Event $event): string;

    public function unserialize(string $serializedEvent): Event;
}
