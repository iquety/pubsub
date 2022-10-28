<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Iquety\PubSub\Event\EventSignal;
use Iquety\PubSub\Event\Signals;
use RuntimeException;
use Tests\Publisher\TestCase\PhpPublisherTestCase;

class PhpEventClientVerboseTest extends PhpPublisherTestCase
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
    public function sendEvent(): void
    {
        $publisher = $this->phpPublisherFactory([])->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/fake-connection.txt', 'w')
        );

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', $this->eventOneFactory())
        );

        $this->assertStringHasMessages([
            "Publish event labeled as 'event-one' to channel 'channel-one'",
        ], $output);
    }

    /** @test */
    public function sendEventSignal(): void
    {
        $publisher = $this->phpPublisherFactory([])->enableVerboseMode();

        $publisher->useTestSocket(
            fopen(__DIR__ . '/../files/fake-connection.txt', 'w')
        );

        $output = $this->gotcha(
            fn() => $publisher->publish('channel-one', new EventSignal(Signals::STOP))
        );

        $this->assertStringHasMessages([
            "Publish event labeled as 'signal.stop' to channel 'channel-one'",
        ], $output);
    }
}
