<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use DateTimeZone;
use Exception;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Subscriber\EventSubscriber;

/**
 * Publicador de eventos simples para bootstrap de aplicações
 */
class SimpleEventPublisher extends AbstractEventPublisher
{
    /** @var array<string,\Freep\PubSub\Subscriber\EventSubscriber> */
    private array $subscribers = [];

    /** @var array<string,array<string>> */
    private array $subscribersByChannel = [];

    private static ?self $instance = null;

    protected function __construct()
    {
        $this->publishInTimezone(new DateTimeZone('UTC'));
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // OBSERVADOR
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    public function hasSubscribers(string $channel = 'all'): bool
    {
        if ($channel === 'all') {
            return $this->subscribers !== [];
        }

        return ($this->subscribersByChannel[$channel] ?? []) !== [];
    }

    public function reset(): self
    {
        parent::reset();

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

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // PUBLICAÇÃO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    public function publish(string $channel, Event $event): self
    {
        parent::setupErrorHandler();

        $label = $event->label();

        $this->messageFactory(
            $this->getNowTimeString() . "Message labeled as '$label' received on channel '$channel'"
        )->infoLn();

        if ($this->hasSubscribers($channel) === false) {
            $this->messageFactory("There are no subscribers on channel '$channel'")->outputLn();
            return $this;
        }

        if ($event instanceof EventSignal) {
            $this->messageFactory(
                $this->getNowTimeString() .
                "EventSignal type messages have no effect on publisher SimpleEventPublisher"
            )->infoLn();

            return $this;
        }

        $this->publishToSubscribers($channel, $event);

        if ($this->isConsole() === true) {
            $this->messageFactory(
                "Publish event labeled as '$label' to channel '$channel'"
            )->successLn();
        }

        return $this;
    }

    private function publishToSubscribers(string $channel, Event $event): void
    {
        $dispatched = false;

        try {
            $allSubscribers = $this->subscribers($channel);

            $eventDataUtc = $this->convertToStreamData($event, $this->getPublicationTimezone());

            foreach ($allSubscribers as $subscriber) {
                $eventData = $this->convertFromStreamData(
                    $eventDataUtc,
                    $subscriber->receiveInTimezone()
                );

                $eventResolved = $subscriber->eventFactory($event->label(), $eventData);

                if ($eventResolved === null) {
                    continue;
                }

                $this->dispatchTo($subscriber, $eventResolved);

                $dispatched = true;
            }
        } catch (Exception $exception) {
            $this->messageFactory($exception->getMessage())->errorLn();
            return;
        }

        if ($this->hasError() === true) {
            // @codeCoverageIgnoreStart
            $this->messageFactory($this->getErrorMessage())->errorLn();
            return;
            // @codeCoverageIgnoreEnd
        }

        if ($dispatched === false) {
            $this->messageFactory("There are no subscribers who accept this type of event")->outputLn();
        }
    }

    private function dispatchTo(EventSubscriber $subscriber, Event $event): void
    {
        // var_dump($event->ocurredOn()); exit;
        $this->messageFactory(
            "Message dispatched to " . $this->getShortClassName($subscriber::class)
        )->outputLn();

        $subscriber->handleEvent($event);
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // SUPORTE
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    private function addToChannelIndex(string $channel, EventSubscriber $aSubscriber): void
    {
        $this->subscribersByChannel[$channel][$aSubscriber::class] = $aSubscriber::class;
    }

    private function inChannelIndex(string $channel, EventSubscriber $aSubscriber): bool
    {
        return isset($this->subscribersByChannel[$channel]) === true
            && isset($this->subscribersByChannel[$channel][$aSubscriber::class]) === true;
    }
}
