<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Tests\Publisher\TestCase\SimplePublisherTestCase;

class SimpleEventSubscribersTest extends SimplePublisherTestCase
{
    /** @return array<string,array<int,string>> */
    public function hasSubscribersProvider(): array
    {
        $list = [];

        $list['one subscriber'] = [ 'channel-one', '' ];
        $list['two subscribers in same channel'] = [ 'channel-one', 'channel-one' ];
        $list['two subscribers in different channels'] = [ 'channel-one', 'channel-two' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider hasSubscribersProvider
     */
    public function hasSubscribers(string $channelOne, string $channelTwo): void
    {
        // para testar a passagem de instancias
        $publisherOne = $this->simplePublisherFactory([
            ['channel-five', new SubscriberTwo()]
        ]);

        $this->assertTrue($publisherOne->hasSubscribers());

        $publisherTwo = $this->simplePublisherFactory(
            $this->subscriberListFactory($channelOne, $channelTwo)
        );

        $this->assertTrue($publisherTwo->hasSubscribers());
    }

    /** @test */
    public function resetSubscribers(): void
    {
        $publisher = $this->simplePublisherFactory(
            $this->subscriberListFactory('', '')
        );
        $publisher->reset();

        $this->assertFalse($publisher->hasSubscribers());
    }

    /** @test */
    public function subscribers(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberOne::class],
            ['channel-one', SubscriberTwo::class],
            ['channel-two', SubscriberTwo::class]
        ]);

        // os assinante sao indexados pelo tipo
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
        $publisher = $this->simplePublisherFactory([]);

        $this->assertEquals([], $publisher->subscribers());
    }

    /** @test */
    public function subscribersInvalidChannel(): void
    {
        $publisher = $this->simplePublisherFactory([]);

        $this->assertEquals([], $publisher->subscribers('non-existent-channel'));
    }

    /** @test */
    public function unsubscribe(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberOne::class],
            ['channel-one', SubscriberTwo::class],
            ['channel-two', SubscriberTwo::class]
        ]);

        $this->assertCount(2, $publisher->subscribers('channel-one'));
        $this->assertCount(1, $publisher->subscribers('channel-two'));

        $publisher->unsubscribe('channel-one', SubscriberTwo::class);

        $this->assertCount(1, $publisher->subscribers('channel-one'));
        $this->assertCount(1, $publisher->subscribers('channel-two'));
    }

    /** @test */
    public function unsubscribeFromSingleChannel(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberOne::class],
            ['channel-two', SubscriberTwo::class]
        ]);

        $publisher->unsubscribe('channel-one', SubscriberOne::class);

        $this->assertCount(0, $publisher->subscribers('channel-one'));
        $this->assertCount(1, $publisher->subscribers());
    }

    /** @test */
    public function unsubscribeFromEmpty(): void
    {
        $publisher = $this->simplePublisherFactory([]);

        $publisher->unsubscribe('monomon', SubscriberOne::class);

        $this->assertCount(0, $publisher->subscribers());
    }
}
