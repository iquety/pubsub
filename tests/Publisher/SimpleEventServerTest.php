<?php

declare(strict_types=1);

namespace Tests\Publisher;

use RuntimeException;

class SimpleEventServerTest extends SimpleEventTestCase
{
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
