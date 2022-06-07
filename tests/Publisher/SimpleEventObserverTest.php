<?php

declare(strict_types=1);

namespace Tests\Publisher;

use DateTimeImmutable;
use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Signals;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Tests\Example\Events\EventOne;
use Tests\Example\Events\EventTwo;
use Tests\Example\Subscribers\SubscriberError;
use Tests\Example\Subscribers\SubscriberException;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

class SimpleEventObserverTest extends PhpEventTestCase
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
    public function sendEventOne(): void
    {
        $this->eventPublisherFactory(SimpleEventPublisher::class);
        SimpleEventPublisher::instance()->enableTestMode();

        $event = $this->eventFactory('ricardo', '123');

        $output = $this->gotcha(
            fn() => SimpleEventPublisher::instance()->publish('channel-one', $event)
        );

        // Mensagens do publicador
        $this->assertStringHasMessages([
            "Publish event labeled as 'event-one' to channel 'channel-one'",
        ], $output);

        // Mensagens do observador
        $this->assertStringHasMessages([
            "Message labeled as 'event-one' received on channel 'channel-one'",
            "Message dispatched to SubscriberOne",
            "Message dispatched to SubscriberTwo",
        ], $output);

        $subscriberOneHandle = $this->readLastHandleFromFile('subscriber-one-handle.txt');
        $subscriberTwoHandle = $this->readLastHandleFromFile('subscriber-two-handle.txt');

        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberOne recebeu",
            "Tests\Example\Events\EventOne"
        ], $subscriberOneHandle);

        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberTwo recebeu",
            "Tests\Example\Events\EventOne"
        ], $subscriberTwoHandle);
    }

    /** @test */
    public function sendEventTwo(): void
    {
        /** @var SimpleEventPublisher $publisher */
        $this->emptyEventPublisherFactory(SimpleEventPublisher::class)
            ->subscribe('channel-one', SubscriberOne::class) // pode receber EventOne
            ->subscribe('channel-one', SubscriberTwo::class); // pode receber qualquer evento

        SimpleEventPublisher::instance()->enableTestMode();

        $ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
        $event = new EventTwo('ricardo', '123', $ocurredOn);

        $output = $this->gotcha(
            fn() => SimpleEventPublisher::instance()->publish('channel-one', $event)
        );
        $subscriberOneHandle = $this->readLastHandleFromFile('subscriber-one-handle.txt');
        $subscriberTwoHandle = $this->readLastHandleFromFile('subscriber-two-handle.txt');

        // Mensagens do publicador
        $this->assertStringHasMessages([
            "Publish event labeled as 'event-two' to channel 'channel-one'",
        ], $output);

        // Mensagens do observador
        $this->assertStringHasMessages([
            "Message labeled as 'event-two' received on channel 'channel-one'",
            "Message dispatched to SubscriberTwo",
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

    /**
     * Sinais são inúteis em um publicador não baseado em servidor
     * @test
     */
    public function sendEventSignal(): void
    {
        $this->eventPublisherFactory(SimpleEventPublisher::class);

        SimpleEventPublisher::instance()->enableTestMode();

        $event = new EventSignal(Signals::STOP);

        $output = $this->gotcha(
            fn() => SimpleEventPublisher::instance()->publish('channel-one', $event)
        );

        $this->assertStringHasMessages([
            "EventSignal type messages have no effect on publisher SimpleEventPublisher"
        ], $output);
    }

    /**
     * Sinais são inúteis em um publicador não baseado em servidor
     * @test
     */
    public function setupErrorHandlerTwoTimes(): void
    {
        $this->eventPublisherFactory(SimpleEventPublisher::class);

        SimpleEventPublisher::instance()->enableTestMode();

        $event = new EventSignal(Signals::STOP);

        $output = $this->gotcha(
            fn() => SimpleEventPublisher::instance()->publish('channel-one', $event)
        );
        $this->assertStringHasMessages([
            "EventSignal type messages have no effect on publisher SimpleEventPublisher"
        ], $output);

        // executa novamente para passar pelo setupErrorHandler duas vezes
        $output = $this->gotcha(
            fn() => SimpleEventPublisher::instance()->publish('channel-one', $event)
        );
        $this->assertStringHasMessages([
            "EventSignal type messages have no effect on publisher SimpleEventPublisher"
        ], $output);
    }

    /** @test */
    public function receiveEventForNoChannelSubscribers(): void
    {
        $this->emptyEventPublisherFactory(SimpleEventPublisher::class)
            ->subscribe('channel-two', SubscriberOne::class); // pode receber apenas EventOne

        SimpleEventPublisher::instance()->enableTestMode();

        $ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
        $event = new EventTwo('ricardo', '123', $ocurredOn);

        $output = $this->gotcha(
            fn() => SimpleEventPublisher::instance()->publish('channel-one', $event)
        );

        $this->assertStringHasMessages([
            "Message labeled as 'event-two' received on channel 'channel-one'",
            "There are no subscribers on channel 'channel-one'"
        ], $output);
    }

    /** @test */
    public function receiveEventForNoAbleSubscribers(): void
    {
        $this->emptyEventPublisherFactory(SimpleEventPublisher::class)
            ->subscribe('channel-two', SubscriberOne::class); // pode receber apenas EventOne

        SimpleEventPublisher::instance()->enableTestMode();

        $ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
        $event = new EventTwo('ricardo', '123', $ocurredOn);

        $output = $this->gotcha(
            fn() => SimpleEventPublisher::instance()->publish('channel-two', $event)
        );

        $this->assertStringHasMessages([
            "Message labeled as 'event-two' received on channel 'channel-two'",
            "There are no subscribers who accept this type of event"
        ], $output);
    }

    /** @test */
    public function receiveEventOnSubscriberException(): void
    {
        $this->emptyEventPublisherFactory(SimpleEventPublisher::class)
            ->subscribe('channel-one', SubscriberException::class);

        SimpleEventPublisher::instance()->enableTestMode();

        $event = $this->eventFactory('ricardo', '123');
        $output = $this->gotcha(
            fn() => SimpleEventPublisher::instance()->publish('channel-one', $event)
        );

        $this->assertStringHasMessages([
            "Exception in subscriber handle",
        ], $output);
    }

    /** @test */
    public function receiveEventOnSubscriberError(): void
    {
        $this->emptyEventPublisherFactory(SimpleEventPublisher::class)
            ->subscribe('channel-one', SubscriberError::class);

        SimpleEventPublisher::instance()->enableTestMode();

        $event = $this->eventFactory('ricardo', '123', EventOne::class);
        $output = $this->gotcha(
            fn() => SimpleEventPublisher::instance()->publish('channel-one', $event)
        );

        $this->assertStringHasMessages([
            "Error triggered in subscriber handle"
        ], $output);
    }
}
