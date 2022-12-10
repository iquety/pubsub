<?php

declare(strict_types=1);

namespace Iquety\PubSub\Event\Serializer;

use DateTimeImmutable;
use DateTimeZone;
use JsonSerializable;
use RuntimeException;

class JsonEventSerializer implements EventSerializer
{
    /** @param array<string,mixed> $eventData */
    public function serialize(array $eventData): string
    {
        $eventData = $this->serializeInput($eventData);

        return (string)json_encode($eventData, JSON_FORCE_OBJECT);
    }

    /** @return array<string,mixed> */
    public function unserialize(string $eventSerializedData): array
    {
        $eventData = json_decode($eventSerializedData, true);

        $this->assertDecodeError();

        return $this->unserializeOutput($eventData);
    }

    private function serializeInput(array $state): array
    {
        foreach ($state as $name => $value) {
            if (is_array($value) === true) {
                $state[$name] = $this->serializeInput($value);
                continue;
            }

            if (is_object($value) === false) {
                continue;
            }

            if (method_exists($value, 'toArray')) {
                $state[$name] = [
                    'class' => $value::class,
                    'state' => $this->serializeInput($value->toArray())
                ];
            }
        }

        return $state;
    }

    private function unserializeOutput(array $state): array
    {
        foreach ($state as $name => $value) {
            if (is_array($value) === false) {
                continue;
            }

            if (
                array_key_exists('class', $value) === true
                && array_key_exists('state', $value) === true
            ) {
                $state[$name] = $this->objectFactory($value['class'], $value['state']);
                continue;
            }

            if (
                array_key_exists('date', $value) === true
                && array_key_exists('timezone', $value) === true
            ) {
                $state[$name] = $this->dateTimeFactory($value);
                continue;
            }

            $state[$name] = $this->unserializeOutput($value);
        }

        return $state;
    }

    private function objectFactory(string $className, array $state): object
    {
        $state = $this->unserializeOutput($state);

        return new $className(...$state);
    }

    /**
     * @param mixed $eventData
     * @return array<string,mixed>
     */
    protected function dateTimeFactory(array $state): mixed
    {
        return new DateTimeImmutable(
            $state['date'],
            new DateTimeZone($state['timezone'])
        );
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
