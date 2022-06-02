<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use Freep\PubSub\Event\Serializer\PhpEventSerializer;
use Freep\PubSub\Example\Subscribers\SubscriberOne;
use Freep\PubSub\Example\Subscribers\SubscriberTwo;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use PHPUnit\Framework\TestCase;

class SimpleEventPublisherTest extends TestCase
{
    private function eventPublisherFactory(): SimpleEventPublisher
    {
        return (new SimpleEventPublisher())
            ->subscribe('channel-one', SubscriberOne::class)
            ->subscribe('channel-one', SubscriberTwo::class)
            ->subscribe('channel-two', SubscriberTwo::class);
    }

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
    public function useSerializer(): void
    {
        $instance = $this->eventPublisherFactory();

        $this->assertInstanceOf(JsonEventSerializer::class, $instance->getSerializer());

        $instance->useSerializer(new PhpEventSerializer());

        $this->assertInstanceOf(PhpEventSerializer::class, $instance->getSerializer());
    }
}
