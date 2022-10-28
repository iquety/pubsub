<?php

declare(strict_types=1);

namespace Iquety\PubSub;

use Iquety\PubSub\Publisher\EventPublisherLoop;

class EventLoop
{
    public function __construct(private EventPublisherLoop $publisher)
    {
    }

    public static function commandPath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Command';
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
