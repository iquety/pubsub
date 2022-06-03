<?php

declare(strict_types=1);

namespace Tests\Example\Subscribers;

use Freep\PubSub\Event\Event;
use Freep\PubSub\Subscriber\EventSubscriber;
use Freep\Security\Filesystem;

class SubscriberTwo implements EventSubscriber
{
    public function handleEvent(Event $event): void
    {
        $file = new Filesystem(dirname(__DIR__, 2) . '/files');

        $file->setFileContents(
            'subscriber-two-handle.txt',
            __CLASS__ . ' recebeu ' . PHP_EOL . $event::class
        );
    }

    public function subscribedToEventType(): string
    {
        // todos os tipos de eventos serão recebidos por este assinante
        return Event::class;
    }
}
