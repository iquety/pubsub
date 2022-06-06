<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Freep\PubSub\Publisher\PhpEventPublisher;
use RuntimeException;
use Tests\Example\Subscribers\SubscriberError;
use Tests\Example\Subscribers\SubscriberException;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

class PhpEventServerTest extends PhpEventTestCase
{
    public function setUp(): void
    {
        $this->clearLastHandleFile('subscriber-one-handle.txt');
        $this->clearLastHandleFile('subscriber-two-handle.txt');
    }

    public static function tearDownAfterClass(): void
    {
        self::clearFile('subscriber-one-handle.txt');
        self::clearFile('subscriber-two-handle.txt');
    }

    /** @test */
    public function startInvalidServer(): void
    {
        $this->expectException(RuntimeException::class);

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->eventPublisherFactory(PhpEventPublisher::class);

        $publisher->useTestSocket(false);

        $publisher->consumerLoop();
    }

    /** @test */
    public function receiveEventOne(): void
    {
        $socketWithEvent = fopen(__DIR__ . '/../files/channel-one-event-one.txt', 'r');

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class)
            ->subscribe('channel-one', SubscriberOne::class) // pode receber EventOne
            ->subscribe('channel-one', SubscriberTwo::class); // pode receber qualquer evento

        $publisher->useTestSocket($socketWithEvent);

        $output = $this->gotcha(fn() => $publisher->consumerLoop());
        $subscriberOneHandle = $this->readLastHandleFromFile('subscriber-one-handle.txt');
        $subscriberTwoHandle = $this->readLastHandleFromFile('subscriber-two-handle.txt');

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // SubscriberOne e SubscriberTwo podem receber EventOne
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message of type 'EventOne' received on channel 'channel-one'",
            "Message dispatched to SubscriberOne",
            "Message dispatched to SubscriberTwo",
            "The publish/subscriber server has been stopped"
        ], $output);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // SubscriberOne recebeu EventOne
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberOne recebeu",
            "Tests\Example\Events\EventOne"
        ], $subscriberOneHandle);


        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // SubscriberTwo recebeu EventOne
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberTwo recebeu",
            "Tests\Example\Events\EventOne"
        ], $subscriberTwoHandle);
    }

    /** @test */
    public function receiveEventTwo(): void
    {
        $socketWithEvent = fopen(__DIR__ . '/../files/channel-one-event-two.txt', 'r');

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class)
            ->subscribe('channel-one', SubscriberOne::class) // pode receber EventOne
            ->subscribe('channel-one', SubscriberTwo::class); // pode receber qualquer evento

        $publisher->useTestSocket($socketWithEvent);

        $output = $this->gotcha(fn() => $publisher->consumerLoop());
        $subscriberOneHandle = $this->readLastHandleFromFile('subscriber-one-handle.txt');
        $subscriberTwoHandle = $this->readLastHandleFromFile('subscriber-two-handle.txt');

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Somente SubscriberTwo pode receber EventTwo
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message of type 'EventTwo' received on channel 'channel-one'",
            "Message dispatched to SubscriberTwo",
            "The publish/subscriber server has been stopped"
        ], $output);

        $this->assertStringNotHasMessages([
            "Message dispatched to SubscriberOne",
        ], $output);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // SubscriberOne não recebeu EventOne
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $this->assertStringNotHasMessages([
            "Tests\Example\Subscribers\SubscriberOne recebeu",
            "Tests\Example\Events\EventOne"
        ], $subscriberOneHandle);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // SubscriberTwo recebeu EventTwo
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberTwo recebeu",
            "Tests\Example\Events\EventTwo"
        ], $subscriberTwoHandle);
    }

    /** @test */
    public function receiveEventSignal(): void
    {
        $socketWithEvent = fopen(__DIR__ . '/../files/stream-signal.txt', 'r');

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->eventPublisherFactory(PhpEventPublisher::class);

        $publisher->useTestSocket($socketWithEvent);

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message of type 'EventSignal' received on channel 'channel-one'",
            "Message to stop the server received",
            "The publish/subscriber server has been stopped",
        ], $output);
    }

    /** @test */
    public function receiveEventForNoChannelSubscribers(): void
    {
        $socketWithEvent = fopen(__DIR__ . '/../files/channel-one-event-two.txt', 'r');

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class)
            ->subscribe('channel-two', SubscriberOne::class); // pode receber apenas EventOne

        $publisher->useTestSocket($socketWithEvent);

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message of type 'EventTwo' received on channel 'channel-one'",
            "There are no subscribers on channel 'channel-one'",
            "The publish/subscriber server has been stopped",
        ], $output);
    }

    /** @test */
    public function receiveEventForNoAbleSubscribers(): void
    {
        $socketWithEvent = fopen(__DIR__ . '/../files/channel-two-event-two.txt', 'r');

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class)
            ->subscribe('channel-two', SubscriberOne::class); // pode receber apenas EventOne

        $publisher->useTestSocket($socketWithEvent);

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message of type 'EventTwo' received on channel 'channel-two'",
            "There are no subscribers who accept this type of event",
            "The publish/subscriber server has been stopped",
        ], $output);
    }

    /** @test */
    public function receiveCorruptStream(): void
    {
        $socketWithEvent = fopen(__DIR__ . '/../files/channel-one-corrupt-stream.txt', 'r');

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->eventPublisherFactory(PhpEventPublisher::class);

        $publisher->useTestSocket($socketWithEvent);

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
        $socketWithEvent = fopen(__DIR__ . '/../files/channel-one-event-one.txt', 'r');

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class)
            ->subscribe('channel-one', SubscriberException::class);

        $publisher->useTestSocket($socketWithEvent);

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
        $socketWithEvent = fopen(__DIR__ . '/../files/channel-one-event-one.txt', 'r');

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class)
            ->subscribe('channel-one', SubscriberError::class);

        $publisher->useTestSocket($socketWithEvent);

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Error triggered in subscriber handle",
            "The publish/subscriber server has been stopped",
        ], $output);
    }
}
