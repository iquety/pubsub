<?php

declare(strict_types=1);

namespace Plexi\Foundation\Tests\PubSub;

use Closure;
use Freep\PubSub\EventLoop;
use Tests\Example\Subscribers\SubscriberOne;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use PHPUnit\Framework\TestCase;

class EventLoopTest extends TestCase
{
    private function gotcha(Closure $callback): string
    {
        ob_start();
        $callback();
        return (string)ob_get_clean();
    }

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

        $publisher->runInTestMode(fopen(__DIR__ . '/files/fake-stream-signal.txt', 'r'));

        $loop = new EventLoop($publisher);
        $loop->addSubscriber('channel-one', SubscriberOne::class);

        $output = $this->gotcha(fn() => $loop->run());

        $this->assertStringContainsString("The publish/subscriber server has been started", $output);
        $this->assertStringContainsString(
            "Message of type 'EventSignal' received on channel 'channel-one'",
            $output
        );
        $this->assertStringContainsString("Message to stop the server received", $output);
        $this->assertStringContainsString("The publish/subscriber server has been stopped", $output);
    }
}
