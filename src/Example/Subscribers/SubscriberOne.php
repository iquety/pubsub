<?php

declare(strict_types=1);

namespace Freep\PubSub\Example\Subscribers;

use Freep\PubSub\Example\Events\EventOne;
use Freep\PubSub\Subscriber\EventSubscriber;

class SubscriberOne implements EventSubscriber
{
    public function handleEvent(string $aPayload): void
    {
        global $publishedContent;
        $publishedContent = __CLASS__ . ' recebeu ' . PHP_EOL . $aPayload;
    }

    public function subscribedToEventType(): string
    {
        // Apenas eventos deste tipo serão recebidos por este assinante
        return EventOne::class;
    }
}
