<?php

declare(strict_types=1);

namespace Iquety\PubSub\Publisher;

interface EventPublisherLoop extends EventPublisher
{
    public function consumerLoop(): void;
}
