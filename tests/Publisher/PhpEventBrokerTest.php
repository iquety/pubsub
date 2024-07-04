<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Iquety\PubSub\Publisher\PhpEventPublisher;
use RuntimeException;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Tests\Publisher\TestCase\PhpPublisherTestCase;

class PhpEventBrokerTest extends PhpPublisherTestCase
{
    /** @test */
    public function hostAndPortDefault(): void
    {
        $publisher = new PhpEventPublisher();

        $this->assertEquals('localhost', $publisher->getHost());
        $this->assertEquals(8080, $publisher->getPort());
    }

    /** @test */
    public function hostAndPortSettedSame(): void
    {
        $publisher = new PhpEventPublisher('localhost', 8080);

        $this->assertEquals('localhost', $publisher->getHost());
        $this->assertEquals(8080, $publisher->getPort());
    }

    /** @test */
    public function hostAndPortSettedOther(): void
    {
        $publisher = new PhpEventPublisher('www.google.com', 888);

        $this->assertEquals('www.google.com', $publisher->getHost());
        $this->assertEquals(888, $publisher->getPort());
    }

    /** @test */
    public function startInvalidServer(): void
    {
        $this->expectException(RuntimeException::class);

        $publisher = $this->phpPublisherFactory([]);

        $publisher->useTestSocket(false);

        $publisher->consumerLoop();
    }

    /** @test */
    public function receiveEventOne(): void
    {
        $publisher = $this->phpPublisherFactory([
            ['channel-one', SubscriberOne::class], // pode receber EventOne
            ['channel-one', SubscriberTwo::class]  // pode receber EventOne e EventTwo
        ]);

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/channel-one-event-one.txt', 'r')
        );

        $publisher->consumerLoop();

        // SubscriberOne recebeu EventOne
        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberOne",
            "recebeu: Tests\Example\Events\EventOne"
        ], $this->readLastHandleFromFile('subscriber-one-handle.txt'));

        // SubscriberTwo recebeu EventOne
        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberTwo",
            "recebeu: Tests\Example\Events\EventOne"
        ], $this->readLastHandleFromFile('subscriber-two-handle.txt'));
    }

    /** @test */
    public function receiveEventTwo(): void
    {
        $publisher = $this->phpPublisherFactory([
            ['channel-one', SubscriberOne::class], // pode receber EventOne
            ['channel-one', SubscriberTwo::class]  // pode receber EventOne e EventTwo
        ]);

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/channel-one-event-two.txt', 'r')
        );

        $publisher->consumerLoop();

        // SubscriberOne não recebeu EventOne
        $this->assertStringNotHasMessages([
            "Tests\Example\Subscribers\SubscriberOne",
            "recebeu: Tests\Example\Events\EventOne"
        ], $this->readLastHandleFromFile('subscriber-one-handle.txt'));

        // SubscriberTwo recebeu EventTwo
        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberTwo",
            "recebeu: Tests\Example\Events\EventTwo"
        ], $this->readLastHandleFromFile('subscriber-two-handle.txt'));
    }
}
