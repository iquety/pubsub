<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use Freep\PubSub\Event\Event;

class PhpEventSerializer implements EventSerializer
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
            . serialize($aEvent);
    }

    public function unserialize(string $aSerializedEvent): Event
    {
        if ($this->unpacked === []) {
            $this->eventType($aSerializedEvent);
        }

        return unserialize($this->unpacked['serialized']);
    }
}
