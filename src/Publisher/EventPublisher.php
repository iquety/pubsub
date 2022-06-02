<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use Freep\PubSub\Event\Event;
use Freep\PubSub\Event\Serializer\EventSerializer;
use Freep\PubSub\Subscriber\EventSubscriber;

interface EventPublisher
{
    public function consumerLoop(): void;

    public function hasSubscribers(string $channel = 'all'): bool;

    public function publish(string $channel, Event $event): self;

    public function reset(): self;

    public function subscribe(string $channel, string $subscriberSignatute): self;

    /** @return array<\Freep\PubSub\Subscriber\EventSubscriber> */
    public function subscribers(string $channel = 'all'): array;

    public function unsubscribe(string $channel, string $subscriberSignature): self;

    public function useSerializer(EventSerializer $serializer): self;

    public function getSerializer(): EventSerializer;
}
