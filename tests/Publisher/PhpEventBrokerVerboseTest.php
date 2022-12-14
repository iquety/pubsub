<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Tests\Example\Subscribers\SubscriberError;
use Tests\Example\Subscribers\SubscriberException;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Tests\Publisher\TestCase\PhpPublisherTestCase;

class PhpEventBrokerVerboseTest extends PhpPublisherTestCase
{
    /** @test */
    public function receiveEventOne(): void
    {
        $publisher = $this->phpPublisherFactory([
            ['channel-one', new SubscriberOne()], // pode receber EventOne
            ['channel-one', SubscriberTwo::class]  // pode receber EventOne e EventTwo
        ])->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/channel-one-event-one.txt', 'r')
        );

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        // SubscriberOne e SubscriberTwo podem receber EventOne
        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message labeled as 'event-one' received on channel 'channel-one'",
            "Message dispatched to SubscriberOne",
            "Message dispatched to SubscriberTwo",
            "The publish/subscriber server has been stopped"
        ], $output);
    }

    /** @test */
    public function receiveEventTwo(): void
    {
        $publisher = $this->phpPublisherFactory([
            ['channel-one', SubscriberOne::class], // pode receber EventOne
            ['channel-one', new SubscriberTwo()]  // pode receber EventOne e EventTwo
        ])->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/channel-one-event-two.txt', 'r')
        );

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        // Somente SubscriberTwo pode receber EventTwo
        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message labeled as 'event-two' received on channel 'channel-one'",
            "Message dispatched to SubscriberTwo",
            "The publish/subscriber server has been stopped"
        ], $output);

        $this->assertStringNotHasMessages([
            "Message dispatched to SubscriberOne",
        ], $output);
    }

    /** @return array<string,array<int,string>> */
    public function receiveEventSignalProvider(): array
    {
        $list = [];

        $list['two subscribers'] = [ 'channel-one', 'channel-one' ];
        $list['zero subscribers'] = [ '', '' ];

        return $list;
    }

    /**
     * @test
     * @dataProvider receiveEventSignalProvider
     */
    public function receiveEventSignal(string $channelOne, string $channelTwo): void
    {
        $publisher = $this->phpPublisherFactory(
            $this->subscriberListFactory($channelOne, $channelTwo)
        )->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/stream-signal.txt', 'r')
        );

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message labeled as 'signal.stop' received on channel 'channel-one'",
            "Message to stop the server received",
            "The publish/subscriber server has been stopped",
        ], $output);
    }

    /** @test */
    public function receiveEventForNoChannelSubscribers(): void
    {
        $publisher = $this->phpPublisherFactory([
            ['channel-two', SubscriberOne::class]
        ])->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/channel-one-event-two.txt', 'r')
        );

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message labeled as 'event-two' received on channel 'channel-one'",
            "There are no subscribers on channel 'channel-one'",
            "The publish/subscriber server has been stopped",
        ], $output);
    }

    /** @test */
    public function receiveEventForNoAbleSubscribers(): void
    {
        $publisher = $this->phpPublisherFactory([
            ['channel-two', SubscriberOne::class]
        ])->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/channel-two-event-two.txt', 'r')
        );

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message labeled as 'event-two' received on channel 'channel-two'",
            "There are no subscribers who accept this type of event",
            "The publish/subscriber server has been stopped",
        ], $output);
    }

    /** @test */
    public function receiveCorruptStream(): void
    {
        $publisher = $this->phpPublisherFactory([])->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/channel-one-corrupt-stream.txt', 'r')
        );

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "The stream received is corrupt",
            "The publish/subscriber server has been stopped",
        ], $output);
    }

    /** @test */
    public function receiveEventOnSubscriberException(): void
    {
        $publisher = $this->phpPublisherFactory([
            ['channel-one', SubscriberException::class]
        ])->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/channel-one-event-one.txt', 'r')
        );

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Exception in subscriber handle",
            "The publish/subscriber server has been stopped",
        ], $output);
    }

    /** @test */
    public function receiveEventOnSubscriberError(): void
    {
        $publisher = $this->phpPublisherFactory([
            ['channel-one', SubscriberError::class]
        ])->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/channel-one-event-one.txt', 'r')
        );

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Error triggered in subscriber handle",
            "The publish/subscriber server has been stopped",
        ], $output);
    }
}
