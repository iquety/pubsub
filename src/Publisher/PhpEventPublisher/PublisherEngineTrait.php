<?php

declare(strict_types=1);

namespace Iquety\PubSub\Publisher\PhpEventPublisher;

use DateTimeZone;
use Exception;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Event\EventSignal;
use Iquety\PubSub\Event\Signals;
use Iquety\PubSub\Subscriber\EventSubscriber;
use RuntimeException;

/**
 * Publicador de eventos para servidoer baseado em PHP
 * @method PhpEventPublisher subscribe(string $channel, string $subscriberSignatute)
 * @method PhpEventPublisher enableVerboseMode()
 */
trait PublisherEngineTrait
{
    /**
     * @return resource
     * @throws RuntimeException
     */
    protected function createClient()
    {
        $address = $this->getAddressString();

        $socketClient = $this->isTestMode() === true
            ? $this->getCustomSocket() ?? false
            : stream_socket_client($address);

        if ($socketClient === false || $this->hasError() === true) {
            throw new RuntimeException($this->getErrorMessage(), $this->getErrorCode());
        }

        stream_set_blocking($socketClient, false);

        return $socketClient;
    }


    /** @override */
    public function publish(string $channel, Event $event): self
    {
        $socketClient = $this->createClient();

        $this->setActivityFor($socketClient, $channel, $event);

        if ($this->isConsole() === true) {
            $this->messageFactory(
                "Publish event labeled as '" . $event->label() . "'" .
                " to channel '$channel' in " . $this->getAddressString()
            )->successLn();
        }

        return $this;
    }

    /** @param resource $socketClient */
    protected function setActivityFor($socketClient, string $channel, Event $event): void
    {
        $eventData = $this->convertToStreamData($event, $this->getPublicationTimezone());

        $eventContents = ($event instanceof EventSignal)
            ? $event->label()
            : $this->getSerializer()->serialize($eventData);

        $payload = $channel
            . PHP_EOL . PHP_EOL
            . $event->label()
            . PHP_EOL . PHP_EOL
            . $eventContents . PHP_EOL;

        fwrite($socketClient, $payload);
        fclose($socketClient);
    }
}
