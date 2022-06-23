<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Tests\Publisher\TestCase\SimplePublisherTestCase;

class SimpleEventObserverTest extends SimplePublisherTestCase
{
    /** @test */
    public function sendEventOne(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberOne::class], // pode receber EventOne
            ['channel-one', SubscriberTwo::class], // pode receber EventOne e EventTwo
            ['channel-two', SubscriberTwo::class]  // pode receber EventOne e EventTwo
        ]);

        $publisher->publish('channel-one', $this->eventOneFactory());

        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberOne",
            "recebeu: Tests\Example\Events\EventOne",
        ], $this->readLastHandleFromFile('subscriber-one-handle.txt'));

        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberTwo",
            "recebeu: Tests\Example\Events\EventOne",
        ], $this->readLastHandleFromFile('subscriber-two-handle.txt'));
    }

    /** @test */
    public function sendEventTwo(): void
    {
        $publisher = $this->simplePublisherFactory([
            ['channel-one', SubscriberOne::class], // pode receber EventOne
            ['channel-one', SubscriberTwo::class]  // pode receber EventOne e EventTwo
        ]);

        $publisher->publish('channel-one', $this->eventTwoFactory());

        // SubscriberOne NÃO RECEBEU EventTwo
        $this->assertStringNotHasMessages([
            "Tests\Example\Subscribers\SubscriberOne",
            "recebeu: Tests\Example\Events\EventTwo",
        ], $this->readLastHandleFromFile('subscriber-one-handle.txt'));

        // SubscriberTwo RECEBEU EventTwo
        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberTwo",
            "recebeu: Tests\Example\Events\EventTwo",
        ], $this->readLastHandleFromFile('subscriber-two-handle.txt'));
    }
}
