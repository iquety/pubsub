<?php

declare(strict_types=1);

namespace Iquety\PubSub\Event\Serializer;

use RuntimeException;

class JsonEventSerializer implements EventSerializer
{
    /** @param array<string,mixed> $eventData */
    public function serialize(array $eventData): string
    {
        return (string)json_encode($eventData, JSON_FORCE_OBJECT);
    }

    /** @return array<string,mixed> */
    public function unserialize(string $eventSerializedData): array
    {
        $eventData = json_decode($eventSerializedData, true);
        $this->assertDecodeError();

        return $eventData;
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
