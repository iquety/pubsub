<?php

declare(strict_types=1);

namespace Tests\Publisher\TestCase;

use Iquety\PubSub\Publisher\SimpleEventPublisher;
use Iquety\PubSub\Subscriber\EventSubscriber;

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
     * @param array<int,array<mixed>> $subcriberList
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function simplePublisherFactory(array $subcriberList): SimpleEventPublisher
    {
        SimpleEventPublisher::instance()->reset();

        foreach ($subcriberList as $subscriber) {
            /** @var string */
            $channel = $subscriber[0];

            /** @var string|EventSubscriber */
            $signature = $subscriber[1];

            SimpleEventPublisher::instance()->subscribe($channel, $signature);
        }

        return SimpleEventPublisher::instance();
    }
}
