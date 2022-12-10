<?php

declare(strict_types=1);

namespace Tests\Event\Serializer;

use DateTimeImmutable;
use Iquety\PubSub\Event\Serializer\JsonEventSerializer;
use RuntimeException;
use Tests\Example\Events\Family;
use Tests\Example\Events\Name;
use Tests\Example\Events\Serial;
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
        $event = $this->eventThrFactory();
        $streamData = $event->toArray();

        $this->assertInstanceOf(Name::class, $streamData['name']);
        $this->assertInstanceOf(Serial::class, $streamData['serial']);
        $this->assertInstanceOf(DateTimeImmutable::class, $streamData['schedule']);
        $this->assertEquals(
            '2022-05-22 17:00:00.777777',
            $streamData['schedule']->format('Y-m-d H:i:s.u')
        );
        $this->assertInstanceOf(DateTimeImmutable::class, $streamData['occurredOn']);

        $serializedEvent = $this->factory()->serialize($streamData);

        $decoded = json_decode($serializedEvent, true);

        $this->assertEquals([
            'class' => Name::class,
            'state' => [
                'name' => 'ricardo',
                'lastName' => [
                    'class' => Family::class,
                    'state' => ['name' => 'Gates']
                ]
            ]
        ], $decoded['name']);

        $this->assertEquals([
            'class' => Serial::class,
            'state' => ['serial' => 55544433322]
        ], $decoded['serial']);

        $this->assertEquals([
            'date' => '2022-05-22 17:00:00.777777',
            'timezone_type' => 3,
            'timezone' => 'UTC'
        ], $decoded['schedule']);

        $this->assertArrayHasKey('date', $decoded['occurredOn']);
        $this->assertArrayHasKey('timezone_type', $decoded['occurredOn']);
        $this->assertArrayHasKey('timezone', $decoded['occurredOn']);

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
