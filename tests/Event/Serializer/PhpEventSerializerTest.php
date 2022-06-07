<?php

declare(strict_types=1);

namespace Tests\Event\Serializer;

use Freep\PubSub\Event\Serializer\PhpEventSerializer;
use RuntimeException;
use Tests\Example\Events\EventOne;
use Tests\TestCase;

class PhpEventSerializerTest extends TestCase
{
    /** @test */
    public function serializationAndReconstitution(): void
    {
        $event = $this->eventFactory('ricardo', '123');

        $serializedEvent = (new PhpEventSerializer())->serialize($event->toArray());

        $reconstitution = (new PhpEventSerializer())->unserialize($serializedEvent);

        $this->assertEquals($reconstitution, $event->toArray());
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
