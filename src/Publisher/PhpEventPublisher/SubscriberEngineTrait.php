<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher\PhpEventPublisher;

use Exception;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Event\Signals;
use Freep\PubSub\Subscriber\EventSubscriber;
use RuntimeException;

/**
 * Publicador de eventos para servidoer baseado em PHP
 * @method PhpEventPublisher subscribe(string $channel, string $subscriberSignatute)
 * @method PhpEventPublisher enableVerboseMode()
 */
trait SubscriberEngineTrait
{
    private bool $running = true;

    /** @override */
    public function consumerLoop(): void
    {
        $socketServer = $this->createServer();

        $this->running = true;

        while ($this->running === true) {
            $this->getActivityFrom($socketServer);

            if ($this->isTestMode() === true) {
                $this->running = false;
            }
        }

        fclose($socketServer);

        $this->messageFactory(
            "The publish/subscriber server has been stopped" . PHP_EOL
        )->successLn();
    }

    /**
     * @return resource
     * @throws RuntimeException
     */
    protected function createServer()
    {
        $address = $this->getAddressString();

        $socketServer = $this->isTestMode() === true
            ? $this->getCustomSocket() ?? false
            : stream_socket_server($address); // @codeCoverageIgnore

        if ($socketServer === false || $this->hasError() === true) {
            throw new RuntimeException($this->getErrorMessage(), $this->getErrorCode());
        }

        $this->messageFactory(
            "The publish/subscriber server has been started in " .
            $address . PHP_EOL
        )->successLn();

        return $socketServer;
    }

    /** @param resource $socketServer */
    protected function getActivityFrom($socketServer): void
    {
        $readStream = [ $socketServer ];
        $writeStream = [];

        $streamCount = $this->streamSelect($readStream, $writeStream);
        if ($streamCount === 0) {
            return; // @codeCoverageIgnore
        }

        try {
            $contents = $this->getActivityContents($socketServer);
        } catch (RuntimeException) { // @codeCoverageIgnore
            return; // @codeCoverageIgnore
        }

        $parts = explode(PHP_EOL . PHP_EOL, $contents); // @phpstan-ignore-line

        if (count($parts) !== 3) {
            $this->messageFactory("The stream received is corrupt")->warningLn();
            return;
        }

        $channel = $parts[0];
        $label   = $parts[1];
        $eventSerializedData = trim($parts[2], PHP_EOL);

        $this->messageFactory(
            $this->getNowTimeString() . "Message labeled as '$label' received on channel '$channel'"
        )->infoLn();

        if ($label === Signals::STOP) {
            $this->running = false;

            $this->messageFactory(
                $this->getNowTimeString() . "Message to stop the server received"
            )->infoLn();

            return;
        }

        $this->publishToSubscribers($channel, $label, $eventSerializedData);

        $this->messageFactory('')->outputLn();
    }

    /**
     * @param array<mixed> $readStream
     * @param array<mixed> $writeStream
     * @return int Total de streamings disponíveis
     */
    private function streamSelect(array &$readStream, array &$writeStream): int
    {
        $exceptions = [];

        $streamCount = (int)stream_select($readStream, $writeStream, $exceptions, PHP_INT_MAX);

        usleep(200000);

        return $streamCount;
    }

    /** @param resource $socketServer */
    private function getActivityContents($socketServer): string
    {
        if ($this->isTestMode() === true) {
            return (string)fread($socketServer, 1024);
        }

        // @codeCoverageIgnoreStart
        $connection = stream_socket_accept($socketServer);
        if ($connection === false) {
            throw new RuntimeException('');
        }

        return (string)stream_get_contents($connection);
        // @codeCoverageIgnoreEnd
    }

    private function publishToSubscribers(string $channel, string $label, string $eventSerializedData): void
    {
        if ($this->hasSubscribers($channel) === false) {
            $this->messageFactory("There are no subscribers on channel '$channel'")->outputLn();
            return;
        }

        $dispatched = false;

        $eventDataUtc = $this->getSerializer()->unserialize($eventSerializedData);

        try {
            $allSubscribers = $this->subscribers($channel);

            foreach ($allSubscribers as $subscriber) {
                $eventData = $this->convertFromStreamData(
                    $eventDataUtc,
                    $subscriber->receiveInTimezone()
                );

                $event = $subscriber->eventFactory($label, $eventData);

                if ($event === null) {
                    continue;
                }

                $this->dispatchTo($subscriber, $event);

                $dispatched = true;
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

    /** @override */
    protected function dispatchTo(EventSubscriber $subscriber, Event $event): void
    {
        $this->messageFactory(
            "Message dispatched to " . $this->getShortClassName($subscriber::class)
        )->outputLn();

        $subscriber->handleEvent($event);
    }
}
