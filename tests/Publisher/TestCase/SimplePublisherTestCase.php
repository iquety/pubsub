<?php

declare(strict_types=1);

namespace Tests\Publisher\TestCase;

use DateTimeImmutable;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Tests\Example\Events\EventOne;
use Tests\Example\Events\EventTwo;

class SimplePublisherTestCase extends PublisherTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        SimpleEventPublisher::instance()->reset();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        SimpleEventPublisher::instance()->reset();
    }

    /**
     * @param array<int,array<int,string>>
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function simplePublisherFactory(array $subcriberList): SimpleEventPublisher
    {
        SimpleEventPublisher::instance()->reset();

        foreach ($subcriberList as $subscriber) {
            $channel = $subscriber[0];
            $signature = $subscriber[1];

            SimpleEventPublisher::instance()->subscribe($channel, $signature);
        }

        return SimpleEventPublisher::instance();
    }
}
