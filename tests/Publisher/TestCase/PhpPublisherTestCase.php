<?php

declare(strict_types=1);

namespace Tests\Publisher\TestCase;

use Iquety\PubSub\Publisher\PhpEventPublisher;
use Iquety\PubSub\Subscriber\EventSubscriber;

class PhpPublisherTestCase extends PublisherTestCase
{
    /** @param array<int,array<int,string|EventSubscriber>> $subcriberList */
    protected function phpPublisherFactory(array $subcriberList): PhpEventPublisher
    {
        $instance = new PhpEventPublisher('localhost', 8080);

        foreach ($subcriberList as $subscriber) {
            /** @var string */
            $channel = $subscriber[0];

            /** @var class-string<EventSubscriber> */
            $signature = $subscriber[1];

            $instance->subscribe($channel, $signature);
        }

        return $instance;
    }
}
