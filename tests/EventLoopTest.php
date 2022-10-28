<?php

declare(strict_types=1);

namespace Tests;

use Iquety\PubSub\EventLoop;
use Iquety\PubSub\Publisher\PhpEventPublisher;
use Tests\Example\Subscribers\SubscriberOne;

class EventLoopTest extends TestCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function commandPath(): void
    {
        $path = dirname(__DIR__)
            . DIRECTORY_SEPARATOR . 'src'
            . DIRECTORY_SEPARATOR . 'Command';

        $this->assertEquals($path, EventLoop::commandPath());
    }

    /** @test */
    public function addSubscriber(): void
    {
        $publisher = new PhpEventPublisher();

        $loop = new EventLoop($publisher);
        $loop->addSubscriber('channel-one', SubscriberOne::class);

        $this->assertCount(1, $publisher->subscribers());
    }

    /** @test */
    public function loopReceiveSignal(): void
    {
        $publisher = new PhpEventPublisher();
        $publisher->enableVerboseMode();
        $publisher->useTestSocket(fopen(__DIR__ . '/files/stream-signal.txt', 'r'));

        $loop = new EventLoop($publisher);
        $loop->addSubscriber('channel-one', SubscriberOne::class);

        $output = $this->gotcha(fn() => $loop->run());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message labeled as 'signal.stop' received on channel 'channel-one'",
            "Message to stop the server received",
            "The publish/subscriber server has been stopped",
        ], $output);
    }

    /** @test */
    public function loopReceiveSignalQuiet(): void
    {
        $publisher = new PhpEventPublisher();
        $publisher->useTestSocket(fopen(__DIR__ . '/files/stream-signal.txt', 'r'));

        $loop = new EventLoop($publisher);
        $loop->addSubscriber('channel-one', SubscriberOne::class);

        $output = $this->gotcha(fn() => $loop->run());

        $this->assertEquals("", $output);
    }
}
