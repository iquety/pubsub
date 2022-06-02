<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Closure;
use DateTimeImmutable;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use Freep\PubSub\Event\Serializer\PhpEventSerializer;
use Freep\PubSub\Event\Signals;
use Freep\PubSub\Example\Events\EventOne;
use Freep\PubSub\Example\Subscribers\SubscriberOne;
use Freep\PubSub\Example\Subscribers\SubscriberTwo;
use Freep\PubSub\Publisher\EventPublisher;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SimpleEventServerTest extends TestCase
{
    private function eventPublisherFactory(): SimpleEventPublisher
    {
        return (new SimpleEventPublisher())
            ->subscribe('channel-one', SubscriberOne::class)
            ->subscribe('channel-one', SubscriberTwo::class)
            ->subscribe('channel-two', SubscriberTwo::class);
    }

    private function gotcha(Closure $callback): string
    {
        ob_start();
        $callback();
        return (string)ob_get_clean();
    }
    
    /** @test */
    public function invalidServer(): void
    {
        $this->expectException(RuntimeException::class);

        $publisher = $this->eventPublisherFactory();
        $publisher->runInTestMode(false);

        $publisher->consumerLoop();
    }

    /** @test */
    public function consumerLoopSignal(): void
    {
        $publisher = $this->eventPublisherFactory();

        $publisher->runInTestMode(fopen(__DIR__ . '/../files/fake-stream-signal.txt', 'r'));

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringContainsString("The publish/subscriber server has been started", $output);
        $this->assertStringContainsString(
            "Message of type 'EventSignal' received on channel 'channel-one'",
            $output
        );
        $this->assertStringContainsString("Message to stop the server received", $output);
        $this->assertStringContainsString("The publish/subscriber server has been stopped", $output);
    }

    /** @test */
    public function consumerLoopEvent(): void
    {
        $publisher = $this->eventPublisherFactory();

        $publisher->runInTestMode(fopen(__DIR__ . '/../files/fake-stream-event.txt', 'r'));

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringContainsString("The publish/subscriber server has been started", $output);
        $this->assertStringContainsString(
            "Message of type 'EventOne' received on channel 'channel-one'",
            $output
        );
        $this->assertStringContainsString("Message dispatched to SubscriberOne", $output);
        $this->assertStringContainsString("Message dispatched to SubscriberTwo", $output);
        $this->assertStringContainsString("The publish/subscriber server has been stopped", $output);
    }

    /** @test */
    public function consumerLoopEventCorrupt(): void
    {
        $publisher = $this->eventPublisherFactory();

        $publisher->runInTestMode(fopen(__DIR__ . '/../files/fake-stream-corrupt.txt', 'r'));

        $output = $this->gotcha(fn() => $publisher->consumerLoop());

        $this->assertStringContainsString("The publish/subscriber server has been started", $output);
        $this->assertStringContainsString("The stream received is corrupt", $output);
        $this->assertStringContainsString("The publish/subscriber server has been stopped", $output);
    }
}
