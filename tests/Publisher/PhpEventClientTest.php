<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Iquety\PubSub\Event\EventSignal;
use Iquety\PubSub\Event\Signals;
use RuntimeException;
use Tests\Publisher\TestCase\PhpPublisherTestCase;

class PhpEventClientTest extends PhpPublisherTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->clearLastEventFile('fake-connection.txt');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::clearFile('fake-connection.txt');
    }

    /** @test */
    public function sendEventToInvalidServer(): void
    {
        $this->expectException(RuntimeException::class);

        $publisher = $this->phpPublisherFactory([]);

        $publisher->useTestSocket(false);

        $publisher->publish('channel-one', $this->eventOneFactory());
    }

    /** @test */
    public function sendEventToUnavailableServer(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Unable to connect to tcp/');

        $publisher = $this->phpPublisherFactory([]);

        $publisher->publish('channel-one', $this->eventOneFactory());
    }

    /** @test */
    public function sendEvent(): void
    {
        $publisher = $this->phpPublisherFactory([]);

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/fake-connection.txt', 'w')
        );

        $event = $this->eventOneFactory();

        $publisher->publish('channel-one', $event);

        $this->assertEquals([
            'channel'   => 'channel-one',
            'label'     => 'event-one',
            'eventData' => $publisher->getSerializer()->serialize($this->getPlainEventValues($event))
        ], $this->readLastEventFromFile('fake-connection.txt'));
    }

    /** @test */
    public function sendEventSignal(): void
    {
        $publisher = $this->phpPublisherFactory([]);

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/fake-connection.txt', 'w')
        );

        $publisher->publish('channel-one', new EventSignal(Signals::STOP));

        $this->assertEquals([
            'channel'   => 'channel-one',
            'label'     => 'signal.stop',
            'eventData' => Signals::STOP
        ], $this->readLastEventFromFile('fake-connection.txt'));
    }
}
