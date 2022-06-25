<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use DateTimeZone;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Event\Serializer\EventSerializer;
use Freep\PubSub\Subscriber\EventSubscriber;

interface EventPublisher
{
    public function enableVerboseMode(): self;

    public function getPublicationTimezone(): DateTimeZone;

    public function hasSubscribers(string $channel = 'all'): bool;

    public function isVerboseMode(): bool;

    public function publish(string $channel, Event $event): self;

    public function publishInTimezone(DateTimeZone $timezone): self;

    public function reset(): self;

    public function subscribe(string $channel, string $subscriberSignatute): self;

    /** @return array<\Freep\PubSub\Subscriber\EventSubscriber> */
    public function subscribers(string $channel = 'all'): array;

    public function unsubscribe(string $channel, string $subscriberSignature): self;

    public function useSerializer(EventSerializer $serializer): self;

    public function getSerializer(): EventSerializer;
}
