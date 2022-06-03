<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use Error;
use Exception;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Subscriber\EventSubscriber;
use Throwable;

abstract class PhpEventPublisher extends AbstractEventPublisher
{
    /** @var array<string,\Freep\PubSub\Subscriber\EventSubscriber> */
    private array $subscribers = [];

    /** @var array<string,array<string>> */
    private array $subscribersByChannel = [];

    public function hasSubscribers(string $channel = 'all'): bool
    {
        if ($channel === 'all') {
            return $this->subscribers !== [];
        }

        return ($this->subscribersByChannel[$channel] ?? []) !== [];
    }

    public function reset(): self
    {
        $this->subscribers = [];
        $this->subscribersByChannel = [];
        return $this;
    }

    public function subscribe(string $channel, string $subscriberSignatute): self
    {
        $subscriber = new $subscriberSignatute();

        if (isset($this->subscribersByChannel[$channel]) === false) {
            $this->subscribersByChannel[$channel] = [];
        }

        $this->addToChannelIndex($channel, $subscriber);
        $this->subscribers[$subscriber::class] = $subscriber;
        return $this;
    }

    private function addToChannelIndex(string $channel, EventSubscriber $aSubscriber): void
    {
        $this->subscribersByChannel[$channel][$aSubscriber::class] = $aSubscriber::class;
    }

    private function inChannelIndex(string $channel, EventSubscriber $aSubscriber): bool
    {
        return isset($this->subscribersByChannel[$channel]) === true
            && isset($this->subscribersByChannel[$channel][$aSubscriber::class]) === true;
    }

    /** @return array<string,\Freep\PubSub\Subscriber\EventSubscriber> */
    public function subscribers(string $channel = 'all'): array
    {
        if ($channel === 'all') {
            return $this->subscribers;
        }

        if (isset($this->subscribersByChannel[$channel]) === false) {
            return [];
        }

        return array_filter(
            $this->subscribers,
            fn($subscriber) => $this->inChannelIndex($channel, $subscriber)
        );
    }

    public function unsubscribe(string $channel, string $subscriberSignature): self
    {
        $isSingleChannel = $this->isSingleChannelSubscribe($subscriberSignature);

        // remove apenas do indice de canais
        if (isset($this->subscribersByChannel[$channel][$subscriberSignature]) === true) {
            unset($this->subscribersByChannel[$channel][$subscriberSignature]);
        }

        if ($isSingleChannel === false) {
            return $this;
        }

        // se estiver em um unico canal, remove também do indice principal
        if (isset($this->subscribers[$subscriberSignature]) === true) {
            unset($this->subscribers[$subscriberSignature]);
        }

        return $this;
    }

    protected function isSingleChannelSubscribe(string $subscriberSignature): bool
    {
        $incidences = 0;

        foreach ($this->subscribersByChannel as $subscriberList) {
            if (isset($subscriberList[$subscriberSignature]) === false) {
                continue;
            }

            $incidences++;
        }

        return $incidences === 1;
    }

    protected function publishToSubscribers(string $channel, string $aPayload): void
    {
        if ($this->hasSubscribers($channel) === false) {
            $this->messageFactory("There are no subscribers on channel '$channel'")->outputLn();
            return;
        }

        $dispatched = false;

        try {
            $eventType = null;

            $allSubscribers = $this->subscribers($channel);
            foreach ($allSubscribers as $subscriber) {
                $subscribedToType = $subscriber->subscribedToEventType();

                if ($subscribedToType === Event::class) {
                    $this->dispatchTo($subscriber, $aPayload);
                    $dispatched = true;
                    continue;
                }

                if ($eventType === null) {
                    $eventType = $this->getSerializer()->getEventType($aPayload);
                }

                if ($eventType === $subscribedToType) {
                    $this->dispatchTo($subscriber, $aPayload);
                    $dispatched = true;
                }
            }
        } catch (Exception $exception) {
            $this->messageFactory($exception->getMessage())->errorLn();
            return;
        }

        if ($this->hasError() === true) {
            $this->messageFactory($this->getErrorMessage())->errorLn();
            return;
        }

        if ($dispatched === false) {
            $this->messageFactory("There are no subscribers who accept this type of event")->outputLn();
        }
    }

    private function dispatchTo(EventSubscriber $subscriber, string $aPayload): void
    {
        $this->messageFactory(
            "Message dispatched to " . $this->getShortClassName($subscriber::class)
        )->outputLn();

        $subscriber->handleEvent($this->getSerializer()->unserialize($aPayload));
    }
}
