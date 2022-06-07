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
        $this->eventPublisherFactory(SimpleEventPublisher::class);

        $this->assertTrue(SimpleEventPublisher::instance()->hasSubscribers());
    }

    /** @test */
    public function reset(): void
    {
        $this->eventPublisherFactory(SimpleEventPublisher::class);

        $this->assertTrue(SimpleEventPublisher::instance()->hasSubscribers());

        SimpleEventPublisher::instance()->reset();

        $this->assertFalse(SimpleEventPublisher::instance()->hasSubscribers());
    }

    /** @test */
    public function subscribers(): void
    {
        $this->eventPublisherFactory(SimpleEventPublisher::class);

        // os inscritos sao indexados pelo tipo
        $this->assertEquals([
            SubscriberOne::class => new SubscriberOne(),
            SubscriberTwo::class => new SubscriberTwo()
        ], SimpleEventPublisher::instance()->subscribers());

        // e também por canal
        $this->assertCount(2, SimpleEventPublisher::instance()->subscribers('channel-one'));
        $this->assertCount(1, SimpleEventPublisher::instance()->subscribers('channel-two'));
    }

    /** @test */
    public function subscribersEmpty(): void
    {

        $this->emptyEventPublisherFactory(SimpleEventPublisher::class);

        $this->assertEquals([], SimpleEventPublisher::instance()->subscribers());
    }

    /** @test */
    public function subscribersByChannelEmpty(): void
    {
        $this->emptyEventPublisherFactory(SimpleEventPublisher::class);

        $this->assertEquals([], SimpleEventPublisher::instance()->subscribers('monomon'));
    }

    /** @test */
    public function unsubscribe(): void
    {
        $this->eventPublisherFactory(SimpleEventPublisher::class);

        $this->assertCount(2, SimpleEventPublisher::instance()->subscribers('channel-one'));
        $this->assertCount(1, SimpleEventPublisher::instance()->subscribers('channel-two'));

        SimpleEventPublisher::instance()->unsubscribe('channel-one', SubscriberTwo::class);

        $this->assertCount(1, SimpleEventPublisher::instance()->subscribers('channel-one'));
        $this->assertCount(1, SimpleEventPublisher::instance()->subscribers('channel-two'));
    }

    /** @test */
    public function unsubscribeFromSingleChannel(): void
    {
        $this->emptyEventPublisherFactory(SimpleEventPublisher::class);

        SimpleEventPublisher::instance()->subscribe('channel-one', SubscriberOne::class);
        SimpleEventPublisher::instance()->subscribe('channel-two', SubscriberTwo::class);

        SimpleEventPublisher::instance()->unsubscribe('channel-one', SubscriberOne::class);

        $this->assertCount(0, SimpleEventPublisher::instance()->subscribers('channel-one'));
        $this->assertCount(1, SimpleEventPublisher::instance()->subscribers());
    }

    /** @test */
    public function unsubscribeFromEmpty(): void
    {
        $this->emptyEventPublisherFactory(SimpleEventPublisher::class);

        SimpleEventPublisher::instance()->unsubscribe('monomon', SubscriberOne::class);

        $this->assertCount(0, SimpleEventPublisher::instance()->subscribers());
    }

    /** @test */
    public function useSerializer(): void
    {
        $this->eventPublisherFactory(SimpleEventPublisher::class);

        $this->assertInstanceOf(
            JsonEventSerializer::class,
            SimpleEventPublisher::instance()->getSerializer()
        );

        SimpleEventPublisher::instance()->useSerializer(new PhpEventSerializer());

        $this->assertInstanceOf(
            PhpEventSerializer::class,
            SimpleEventPublisher::instance()->getSerializer()
        );
    }
}
