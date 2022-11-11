<?php

declare(strict_types=1);

namespace Tests\Event\Serializer;

use DateTimeImmutable;
use Iquety\PubSub\Event\Serializer\JsonEventSerializer;
use RuntimeException;
use Tests\Example\Events\EventOne;
use Tests\TestCase;

class JsonEventSerializerTest extends TestCase
{
    private function factory(): JsonEventSerializer
    {
        return new JsonEventSerializer();
    }

    /** @test */
    public function serializationAndReconstitution(): void
    {
        $event = $this->eventOneFactory();
        $streamData = $event->toArray();

        $serializedEvent = $this->factory()->serialize($streamData);
        $reconstitution = $this->factory()->unserialize($serializedEvent);

        $this->assertEquals($reconstitution, $streamData);
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
