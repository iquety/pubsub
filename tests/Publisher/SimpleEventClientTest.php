<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Signals;
use Tests\Example\Events\EventOne;
use RuntimeException;

class SimpleEventClientTest extends SimpleEventTestCase
{
    public function tearDown(): void
    {
        $this->clearLastEventFile('fake-connection.txt');
    }

    /** @test */
    public function sendEventToInvalidServer(): void
    {
        $this->expectException(RuntimeException::class);

        $publisher = $this->eventPublisherFactory();
        $publisher->useTestSocket(false);

        $publisher->publish(
            'channel-one',
            $this->eventFactory('ricardo', '123')
        );
    }

    /** @test */
    public function sendEventToUnavailableServer(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Unable to connect to tcp/');

        $publisher = $this->eventPublisherFactory();

        $publisher->publish(
            'channel-one',
            $this->eventFactory('ricardo', '123')
        );
    }

    /** @test */
    public function sendEvent(): void
    {
        $publisher = $this->eventPublisherFactory();

        $publisher->useTestSocket(fopen(__DIR__ . '/../files/fake-connection.txt', 'w'));

        $event = $this->eventFactory('ricardo', '123');
        $publisher->publish('channel-one', $event);

        $lastEvent = $this->readLastEventFromFile('fake-connection.txt');
        $this->assertEquals($lastEvent['channel'], 'channel-one');
        $this->assertEquals($lastEvent['type'], EventOne::class);
        $this->assertEquals($lastEvent['payload'], $publisher->getSerializer()->serialize($event));
    }

    /** @test */
    public function sendEventSignal(): void
    {
        $publisher = $this->eventPublisherFactory();

        $publisher->useTestSocket(fopen(__DIR__ . '/../files/fake-connection.txt', 'w'));

        $event = new EventSignal(Signals::STOP);
        $publisher->publish('channel-one', $event);

        $lastEvent = $this->readLastEventFromFile('fake-connection.txt');
        $this->assertEquals($lastEvent['channel'], 'channel-one');
        $this->assertEquals($lastEvent['type'], EventSignal::class);
        $this->assertEquals($lastEvent['payload'], $event->signal());
    }

    // /** @test */
    // public function publishToAnyone(): void
    // {
    //     $publisher = $this->emptyEventPublisherFactory();

    //     $publisher->useTestSocket(fopen(__DIR__ . '/../files/fake-connection.txt', 'w'));

    //     $event = $this->eventFactory('ricardo', '123');
    //     $publisher->publish('channel-not-exists', $event);

    //     $lastEvent = $this->readLastEventFromFile('fake-connection.txt');
    //     $this->assertEquals($lastEvent['channel'], 'channel-not-exists');
    //     $this->assertEquals($lastEvent['type'], EventOne::class);
    //     $this->assertEquals($lastEvent['payload'], $publisher->getSerializer()->serialize($event));
    // }
}
