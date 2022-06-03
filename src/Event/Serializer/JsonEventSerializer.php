<?php

declare(strict_types=1);

namespace Freep\PubSub\Event\Serializer;

use Freep\PubSub\Event\Event;
use RuntimeException;

class JsonEventSerializer extends AbstractEventSerializer
{
    public function serialize(Event $event): string
    {
        return $event::class
            . PHP_EOL
            . json_encode($event->toArray(), JSON_FORCE_OBJECT);
    }

    public function unserialize(string $serializedEvent): Event
    {
        $className = $this->getEventType($serializedEvent);
        $serialized = $this->getEventSerialized($serializedEvent);

        $arguments = json_decode($serialized, true);
        $this->assertDecodeError();

        return $className::factory($arguments);
    }

    protected function assertDecodeError(): void
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return;
            case JSON_ERROR_SYNTAX:
                $errorMessage = 'Syntax error, malformed JSON';
                break;
            // @codeCoverageIgnoreStart
            case JSON_ERROR_DEPTH:
                $errorMessage = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $errorMessage = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $errorMessage = 'Unexpected control character found';
                break;
            case JSON_ERROR_UTF8:
                $errorMessage = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $errorMessage = 'Unknown error';
            // @codeCoverageIgnoreEnd
        }

        throw new RuntimeException("The json data is corrupted: $errorMessage");
    }
}
