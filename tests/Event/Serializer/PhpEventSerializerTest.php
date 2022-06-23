<?php

declare(strict_types=1);

namespace Tests\Event\Serializer;

use Freep\PubSub\Event\Serializer\PhpEventSerializer;
use RuntimeException;
use Tests\Example\Events\EventOne;
use Tests\TestCase;

class PhpEventSerializerTest extends TestCase
{
    private function factory(): PhpEventSerializer
    {
        return new PhpEventSerializer();
    }

    /** @test */
    public function serializationAndReconstitution(): void
    {
        $event = $this->eventOneFactory();
        $streamData = $this->getPlainEventValues($event);

        $serializedEvent = $this->factory()->serialize($streamData);
        $reconstitution = $this->factory()->unserialize($serializedEvent);

        $this->assertEquals($reconstitution, $streamData);
    }

    /** @test */
    public function unserializeFormatError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "The serialized PHP object is corrupted: unserialize(): Error at offset 0 of 12 bytes"
        );

        (new PhpEventSerializer())->unserialize(
            '{-php-errado'
        );
    }
}
