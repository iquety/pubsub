<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use Freep\PubSub\Event\Event;

class JsonEventSerializer implements EventSerializer
{
    /** @var array<string> */
    private array $unpacked = [];

    public function eventType(string $aSerializedEvent): string
    {
        $unpack = explode("\n", $aSerializedEvent);
        $this->unpacked = [
            'type'       => $unpack[0],
            'serialized' => $unpack[1]
        ];
        return $this->unpacked['type'];
    }

    public function serialize(Event $aEvent): string
    {
        return $aEvent::class
            . "\n"
            . json_encode($aEvent->toArray());
    }

    public function unserialize(string $aSerializedEvent): Event
    {
        return json_decode($this->unpacked['serialized'], true);
    }
}
