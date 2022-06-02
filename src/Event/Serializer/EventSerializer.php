<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use Freep\PubSub\Event\Event;

interface EventSerializer
{
    public function eventType(string $aSerializedEvent): string;

    public function serialize(Event $aEvent): string;

    public function unserialize(string $aSerializedEvent): Event;
}
