<?php

declare(strict_types=1);

namespace Freep\PubSub\Example\Subscribers;

use Freep\PubSub\Event\Event;
use Freep\PubSub\Subscriber\EventSubscriber;

class SubscriberTwo implements EventSubscriber
{
    public function handleEvent(string $aPayload): void
    {
        global $publishedContent;
        $publishedContent = __CLASS__ . ' recebeu ' . PHP_EOL . $aPayload;
    }

    public function subscribedToEventType(): string
    {
        // todos os tipos de eventos serão recebidos por este assinante
        return Event::class;
    }
}
