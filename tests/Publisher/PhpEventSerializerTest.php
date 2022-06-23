<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use Freep\PubSub\Event\Serializer\PhpEventSerializer;
use Tests\Publisher\TestCase\PhpPublisherTestCase;

class PhpEventSerializerTest extends PhpPublisherTestCase
{
    /** @test */
    public function defaultToChanged(): void
    {
        $publisher = $this->phpPublisherFactory([]);

        // default
        $this->assertInstanceOf(JsonEventSerializer::class, $publisher->getSerializer());

        // changed
        $publisher->useSerializer(new PhpEventSerializer());
        $this->assertInstanceOf(PhpEventSerializer::class, $publisher->getSerializer());
    }

    /** @test */
    public function changedToDefault(): void
    {
        $publisher = $this->phpPublisherFactory([]);

        // changed
        $publisher->useSerializer(new PhpEventSerializer());
        $this->assertInstanceOf(PhpEventSerializer::class, $publisher->getSerializer());

        // volta para default
        $publisher->useSerializer(new JsonEventSerializer());
        $this->assertInstanceOf(JsonEventSerializer::class, $publisher->getSerializer());
    }
}
