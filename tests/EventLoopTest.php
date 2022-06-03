<?php

declare(strict_types=1);

namespace Tests;

use Closure;
use Freep\PubSub\EventLoop;
use Tests\Example\Subscribers\SubscriberOne;
use Freep\PubSub\Publisher\SimpleEventPublisher;

class EventLoopTest extends TestCase
{
    /** @test */
    public function addSubscriber(): void
    {
        $publisher = new SimpleEventPublisher();

        $loop = new EventLoop($publisher);
        $loop->addSubscriber('channel-one', SubscriberOne::class);

        $this->assertCount(1, $publisher->subscribers());
    }

    /** @test */
    public function runLoop(): void
    {
        $publisher = new SimpleEventPublisher();

        $publisher->useTestSocket(fopen(__DIR__ . '/files/stream-signal.txt', 'r'));

        $loop = new EventLoop($publisher);
        $loop->addSubscriber('channel-one', SubscriberOne::class);

        $output = $this->gotcha(fn() => $loop->run());

        $this->assertStringHasMessages([
            "The publish/subscriber server has been started",
            "Message of type 'EventSignal' received on channel 'channel-one'",
            "Message to stop the server received",
            "The publish/subscriber server has been stopped",
        ], $output);
    }
}
