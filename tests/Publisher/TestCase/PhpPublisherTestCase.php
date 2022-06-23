<?php

declare(strict_types=1);

namespace Tests\Publisher\TestCase;

use DateTimeImmutable;
use Freep\PubSub\Publisher\PhpEventPublisher;
use Tests\Example\Events\EventOne;
use Tests\Example\Events\EventTwo;

class PhpPublisherTestCase extends PublisherTestCase
{
    /** @param array<int,array<int,string>> */
    protected function phpPublisherFactory(array $subcriberList): PhpEventPublisher
    {
        $instance = new PhpEventPublisher('localhost', 8080);

        foreach ($subcriberList as $subscriber) {
            $channel = $subscriber[0];
            $signature = $subscriber[1];

            $instance->subscribe($channel, $signature);
        }

        return $instance;
    }
}
