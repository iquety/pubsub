<?php

declare(strict_types=1);

namespace Freep\PubSub;

use Freep\PubSub\Publisher\EventPublisher;
use Freep\PubSub\Subscriber\EventSubscriber;

class EventLoop
{
    public function __construct(private EventPublisher $publisher)
    {
    }

    public function addSubscriber(string $channel, string $subscriberSignatute): void
    {
        $this->publisher->subscribe($channel, $subscriberSignatute);
    }

    public function run(): void
    {
        $this->publisher->consumerLoop();
    }
}
