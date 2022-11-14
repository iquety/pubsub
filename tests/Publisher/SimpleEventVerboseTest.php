<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Iquety\PubSub\Event\StopSignal;
use Tests\Example\Subscribers\SubscriberError;
use Tests\Example\Subscribers\SubscriberException;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Tests\Publisher\TestCase\SimplePublisherTestCase;

class SimpleEventVerboseTest extends SimplePublisherTestCase
{
    /** @test */
    public function sendEventOne(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberOne::class], // pode receber EventOne
            ['channel-one', SubscriberTwo::class], // pode receber EventOne e EventTwo
            ['channel-two', SubscriberTwo::class]  // pode receber EventOne e EventTwo
        ])->enableVerboseMode();

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', $this->eventOneFactory())
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
    }

    /** @test */
    public function sendEventTwo(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberOne::class], // pode receber EventOne
            ['channel-one', SubscriberTwo::class]  // pode receber EventOne e EventTwo
        ])->enableVerboseMode();

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', $this->eventTwoFactory())
        );

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
    }

    /**
     * Sinais são inúteis em um publicador não baseado em servidor
     * @test
     */
    public function sendEventSignal(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberOne::class], // pode receber EventOne
            ['channel-one', SubscriberTwo::class], // pode receber EventOne e EventTwo
            ['channel-two', SubscriberTwo::class]  // pode receber EventOne e EventTwo
        ])->enableVerboseMode();

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', new StopSignal())
        );

        $this->assertStringHasMessages([
            "Signal type messages have no effect on publisher SimpleEventPublisher"
        ], $output);
    }

    /**
     * Sinais são inúteis em um publicador não baseado em servidor
     * @test
     */
    public function setupErrorHandlerTwoTimes(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberOne::class], // pode receber EventOne
            ['channel-one', SubscriberTwo::class], // pode receber EventOne e EventTwo
            ['channel-two', SubscriberTwo::class]  // pode receber EventOne e EventTwo
        ])->enableVerboseMode();

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', new StopSignal())
        );

        $this->assertStringHasMessages([
            "Signal type messages have no effect on publisher SimpleEventPublisher"
        ], $output);

        // executa novamente para passar pelo setupErrorHandler duas vezes
        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', new StopSignal())
        );

        $this->assertStringHasMessages([
            "Signal type messages have no effect on publisher SimpleEventPublisher"
        ], $output);
    }

    /** @test */
    public function receiveEventForNoChannelSubscribers(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-two', SubscriberOne::class] // pode receber EventOne
        ])->enableVerboseMode();

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', $this->eventTwoFactory())
        );

        $this->assertStringHasMessages([
            "Message labeled as 'event-two' received on channel 'channel-one'",
            "There are no subscribers on channel 'channel-one'"
        ], $output);
    }

    /** @test */
    public function receiveEventForNoAbleSubscribers(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-two', SubscriberOne::class] // pode receber EventOne
        ])->enableVerboseMode();

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-two', $this->eventTwoFactory())
        );

        $this->assertStringHasMessages([
            "Message labeled as 'event-two' received on channel 'channel-two'",
            "There are no subscribers who accept this type of event"
        ], $output);
    }

    /** @test */
    public function receiveEventOnSubscriberException(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberException::class] // pode receber EventOne
        ])->enableVerboseMode();

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', $this->eventOneFactory())
        );

        $this->assertStringHasMessages([
            "Exception in subscriber handle",
        ], $output);
    }

    /** @test */
    public function receiveEventOnSubscriberError(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberError::class] // pode receber EventOne
        ])->enableVerboseMode();

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', $this->eventOneFactory())
        );

        $this->assertStringHasMessages([
            "Error triggered in subscriber handle"
        ], $output);
    }
}
