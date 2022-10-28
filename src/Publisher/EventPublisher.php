<?php

declare(strict_types=1);

namespace Iquety\PubSub\Publisher;

use DateTimeZone;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Event\Serializer\EventSerializer;
use Iquety\PubSub\Subscriber\EventSubscriber;

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

    /** @return array<\Iquety\PubSub\Subscriber\EventSubscriber> */
    public function subscribers(string $channel = 'all'): array;

    public function unsubscribe(string $channel, string $subscriberSignature): self;

    public function useSerializer(EventSerializer $serializer): self;

    public function getSerializer(): EventSerializer;
}
