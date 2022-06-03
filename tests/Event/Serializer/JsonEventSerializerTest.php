<?php

declare(strict_types=1);

namespace Tests\Event\Serializer;

use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use RuntimeException;
use Tests\Example\Events\EventOne;
use Tests\TestCase;

class JsonEventSerializerTest extends TestCase
{
    /** @test */
    public function serializationAndReconstitution(): void
    {
        $event = $this->eventFactory('ricardo', '123');

        $serializedEvent = (new JsonEventSerializer())->serialize($event);

        $reconstitution = (new JsonEventSerializer())->unserialize($serializedEvent);

        $this->assertEquals($reconstitution, $event);
        $this->assertEquals($reconstitution->toArray(), $event->toArray());
    }

    /** @test */
    public function unserializeFormatError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "The serialized event is corrupted"
        );

        (new JsonEventSerializer())->unserialize(
            '{-json-errado'
        );
    }

    /** @test */
    public function unserializeFormatErrorLastEol(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "The serialized event is corrupted"
        );

        (new JsonEventSerializer())->unserialize(
            EventOne::class . PHP_EOL . '{-json-errado' . PHP_EOL
        );
    }

    /** @test */
    public function unserializeFormatErrorFirstEol(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "The serialized event is corrupted"
        );

        (new JsonEventSerializer())->unserialize(
            PHP_EOL . EventOne::class . PHP_EOL . '{-json-errado'
        );
    }

    /** @test */
    public function unserializeJsonError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "The json data is corrupted: Syntax error, malformed JSON"
        );

        (new JsonEventSerializer())->unserialize(
            EventOne::class . PHP_EOL . '{-json-errado'
        );
    }
}
