<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use Freep\PubSub\Event\Serializer\PhpEventSerializer;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

class SimpleEventPublisherTest extends SimpleEventTestCase
{
    /** @test */
    public function hasSubscribers(): void
    {
        $instance = $this->eventPublisherFactory();

        $this->assertTrue($instance->hasSubscribers());
    }

    /** @test */
    public function reset(): void
    {
        $instance = $this->eventPublisherFactory();

        $this->assertTrue($instance->hasSubscribers());

        $instance->reset();

        $this->assertFalse($instance->hasSubscribers());
    }

    /** @test */
    public function subscribers(): void
    {
        $instance = $this->eventPublisherFactory();

        // os inscritos sao indexados pelo tipo
        $this->assertEquals([
            SubscriberOne::class => new SubscriberOne(),
            SubscriberTwo::class => new SubscriberTwo()
        ], $instance->subscribers());

        // e também por canal
        $this->assertCount(2, $instance->subscribers('channel-one'));
        $this->assertCount(1, $instance->subscribers('channel-two'));
    }

    /** @test */
    public function subscribersEmpty(): void
    {
        $instance = $this->emptyEventPublisherFactory();
        $this->assertEquals([], $instance->subscribers());
    }

    /** @test */
    public function subscribersByChannelEmpty(): void
    {
        $instance = $this->emptyEventPublisherFactory();
        $this->assertEquals([], $instance->subscribers('monomon'));
    }

    /** @test */
    public function unsubscribe(): void
    {
        $instance = $this->eventPublisherFactory();

        $this->assertCount(2, $instance->subscribers('channel-one'));
        $this->assertCount(1, $instance->subscribers('channel-two'));

        $instance->unsubscribe('channel-one', SubscriberTwo::class);

        $this->assertCount(1, $instance->subscribers('channel-one'));
        $this->assertCount(1, $instance->subscribers('channel-two'));
    }

    /** @test */
    public function unsubscribeFromSingleChannel(): void
    {
        $instance = $this->emptyEventPublisherFactory();
        $instance->subscribe('channel-one', SubscriberOne::class);
        $instance->subscribe('channel-two', SubscriberTwo::class);

        $instance->unsubscribe('channel-one', SubscriberOne::class);

        $this->assertCount(0, $instance->subscribers('channel-one'));
        $this->assertCount(1, $instance->subscribers());
    }

    /** @test */
    public function unsubscribeFromEmpty(): void
    {
        $instance = $this->emptyEventPublisherFactory();

        $instance->unsubscribe('monomon', SubscriberOne::class);

        $this->assertCount(0, $instance->subscribers());
    }

    /** @test */
    public function useSerializer(): void
    {
        $instance = $this->eventPublisherFactory();

        $this->assertInstanceOf(JsonEventSerializer::class, $instance->getSerializer());

        $instance->useSerializer(new PhpEventSerializer());

        $this->assertInstanceOf(PhpEventSerializer::class, $instance->getSerializer());
    }
}
