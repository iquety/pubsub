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

        $serializedEvent = (new JsonEventSerializer())->serialize($event->toArray());

        $reconstitution = (new JsonEventSerializer())->unserialize($serializedEvent);

        $this->assertEquals($reconstitution, $event->toArray());
    }

    /** @test */
    public function unserializeFormatError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "The json data is corrupted: Syntax error, malformed JSON"
        );

        (new JsonEventSerializer())->unserialize(
            '{-json-errado'
        );
    }
}
