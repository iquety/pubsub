<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Signals;
use Freep\PubSub\Publisher\PhpEventPublisher;
use Tests\Example\Events\EventOne;
use RuntimeException;

class PhpEventClientTest extends PhpEventTestCase
{
    public function tearDown(): void
    {
        $this->clearLastEventFile('fake-connection.txt');
    }

    /** @test */
    public function sendEventToInvalidServer(): void
    {
        $this->expectException(RuntimeException::class);

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class);
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

        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class);

        $publisher->publish(
            'channel-one',
            $this->eventFactory('ricardo', '123')
        );
    }

    /** @test */
    public function sendEvent(): void
    {
        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class);

        $publisher->useTestSocket(fopen(__DIR__ . '/../files/fake-connection.txt', 'w'));

        $event = $this->eventFactory('ricardo', '123');

        $output = $this->gotcha(fn() => $publisher->publish('channel-one', $event));

        $this->assertStringHasMessages([
            "Publish event of type 'EventOne' to channel 'channel-one'",
        ], $output);

        $lastEvent = $this->readLastEventFromFile('fake-connection.txt');
        $this->assertEquals($lastEvent['channel'], 'channel-one');
        $this->assertEquals($lastEvent['type'], EventOne::class);
        $this->assertEquals($lastEvent['payload'], $publisher->getSerializer()->serialize($event));
    }

    /** @test */
    public function sendEventSignal(): void
    {
        /** @var PhpEventPublisher $publisher */
        $publisher = $this->emptyEventPublisherFactory(PhpEventPublisher::class);

        $publisher->useTestSocket(fopen(__DIR__ . '/../files/fake-connection.txt', 'w'));

        $event = new EventSignal(Signals::STOP);

        $output = $this->gotcha(fn() => $publisher->publish('channel-one', $event));

        $this->assertStringHasMessages([
            "Publish event of type 'EventSignal' to channel 'channel-one'",
        ], $output);

        $lastEvent = $this->readLastEventFromFile('fake-connection.txt');
        $this->assertEquals($lastEvent['channel'], 'channel-one');
        $this->assertEquals($lastEvent['type'], EventSignal::class);
        $this->assertEquals($lastEvent['payload'], $event->signal());
    }
}
