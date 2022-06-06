<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use Freep\PubSub\Event\Serializer\PhpEventSerializer;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Tests\TestCase;

class SimpleEventCommonTest extends TestCase
{
    /** @test */
    public function hasSubscribers(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $publisher = $this->eventPublisherFactory(SimpleEventPublisher::class);

        $this->assertTrue($publisher->hasSubscribers());
    }

    /** @test */
    public function reset(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $publisher = $this->eventPublisherFactory(SimpleEventPublisher::class);

        $this->assertTrue($publisher->hasSubscribers());

        $publisher->reset();

        $this->assertFalse($publisher->hasSubscribers());
    }

    /** @test */
    public function subscribers(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $publisher = $this->eventPublisherFactory(SimpleEventPublisher::class);

        // os inscritos sao indexados pelo tipo
        $this->assertEquals([
            SubscriberOne::class => new SubscriberOne(),
            SubscriberTwo::class => new SubscriberTwo()
        ], $publisher->subscribers());

        // e também por canal
        $this->assertCount(2, $publisher->subscribers('channel-one'));
        $this->assertCount(1, $publisher->subscribers('channel-two'));
    }

    /** @test */
    public function subscribersEmpty(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(SimpleEventPublisher::class);
        $this->assertEquals([], $publisher->subscribers());
    }

    /** @test */
    public function subscribersByChannelEmpty(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(SimpleEventPublisher::class);

        $this->assertEquals([], $publisher->subscribers('monomon'));
    }

    /** @test */
    public function unsubscribe(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $publisher = $this->eventPublisherFactory(SimpleEventPublisher::class);

        $this->assertCount(2, $publisher->subscribers('channel-one'));
        $this->assertCount(1, $publisher->subscribers('channel-two'));

        $publisher->unsubscribe('channel-one', SubscriberTwo::class);

        $this->assertCount(1, $publisher->subscribers('channel-one'));
        $this->assertCount(1, $publisher->subscribers('channel-two'));
    }

    /** @test */
    public function unsubscribeFromSingleChannel(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(SimpleEventPublisher::class);

        $publisher->subscribe('channel-one', SubscriberOne::class);
        $publisher->subscribe('channel-two', SubscriberTwo::class);

        $publisher->unsubscribe('channel-one', SubscriberOne::class);

        $this->assertCount(0, $publisher->subscribers('channel-one'));
        $this->assertCount(1, $publisher->subscribers());
    }

    /** @test */
    public function unsubscribeFromEmpty(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(SimpleEventPublisher::class);

        $publisher->unsubscribe('monomon', SubscriberOne::class);

        $this->assertCount(0, $publisher->subscribers());
    }

    /** @test */
    public function useSerializer(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $publisher = $this->eventPublisherFactory(SimpleEventPublisher::class);

        $this->assertInstanceOf(JsonEventSerializer::class, $publisher->getSerializer());

        $publisher->useSerializer(new PhpEventSerializer());

        $this->assertInstanceOf(PhpEventSerializer::class, $publisher->getSerializer());
    }
}
