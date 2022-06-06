<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

interface EventPublisherLoop extends EventPublisher
{
    public function consumerLoop(): void;
}
