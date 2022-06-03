<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use RuntimeException;

abstract class AbstractEventSerializer implements EventSerializer
{
    /** @var array<string,mixed> */
    private array $unpacked = [];

    public function getEventType(string $serializedEvent): string
    {
        if ($this->unpacked === []) {
            $this->parse($serializedEvent);
        }

        return $this->unpacked['type'];
    }

    protected function getEventSerialized(string $serializedEvent): string
    {
        $this->getEventType($serializedEvent);

        return $this->unpacked['serialized'];
    }

    private function parse(string $serializedEvent): void
    {
        $unpack = explode(PHP_EOL, $serializedEvent);

        if (count($unpack) !== 2) {
            throw new RuntimeException("The serialized event is corrupted");
        }

        $this->unpacked = [
            'type'       => $unpack[0],
            'serialized' => $unpack[1]
        ];
    }
}
