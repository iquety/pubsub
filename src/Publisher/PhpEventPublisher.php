<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use Exception;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Signals;
use Freep\PubSub\Publisher\EventPublisher;
use Freep\PubSub\Subscriber\EventSubscriber;
use RuntimeException;

/**
 * Publicador de eventos para servidoer baseado em PHP
 * @method PhpEventPublisher subscribe(string $channel, string $subscriberSignatute)
 * @method PhpEventPublisher enableVerboseMode()
 */
class PhpEventPublisher extends SimpleEventPublisher implements EventPublisherLoop
{
    /** @var array<string,mixed> */
    private array $config = [];

    private bool $running = true;

    private string $separator = PHP_EOL . PHP_EOL;

    /** @var resource|false|null */
    private $customSocket = null;

    public function __construct(string $host = 'localhost', int $port = 8080)
    {
        $this->config['host'] = $host;
        $this->config['port'] = $port;

        parent::setupErrorHandler();
    }

    /** @param resource|false|null $socket */
    public function useTestSocket($socket): void
    {
        $this->customSocket = $socket;
        $this->enableTestMode();
    }

    /**
     * @return resource
     * @throws RuntimeException
     */
    protected function createClient()
    {
        $address = $this->getAddressString();

        $socketClient = $this->isTestMode() === true
            ? $this->customSocket ?? false
            : stream_socket_client($address);

        if ($socketClient === false || $this->hasError() === true) {
            throw new RuntimeException($this->getErrorMessage(), $this->getErrorCode());
        }

        stream_set_blocking($socketClient, false);

        return $socketClient;
    }

    /**
     * @return resource
     * @throws RuntimeException
     */
    protected function createServer()
    {
        $address = $this->getAddressString();

        $socketServer = $this->isTestMode() === true
            ? $this->customSocket ?? false
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

    private function getAddressString(): string
    {
        return 'tcp://' . $this->config['host'] . ':' . $this->config['port'];
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // OBSERVADOR
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

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

        $parts = explode($this->separator, $contents); // @phpstan-ignore-line

        if (count($parts) !== 3) {
            $this->messageFactory("The stream received is corrupt")->warningLn();
            return;
        }

        $channel = $parts[0];
        $type    = $this->getShortClassName($parts[1]);
        $payload = trim($parts[2], PHP_EOL);

        $this->messageFactory(
            $this->getNowTimeString() . "Message of type '$type' received on channel '$channel'"
        )->infoLn();

        if ($payload === Signals::STOP) {
            $this->running = false;

            $this->messageFactory(
                $this->getNowTimeString() . "Message to stop the server received"
            )->infoLn();

            return;
        }

        $this->publishToSubscribers($channel, $payload);

        $this->messageFactory('')->outputLn();
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

    private function publishToSubscribers(string $channel, string $aPayload): void
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

    /** @override */
    protected function dispatchTo(EventSubscriber $subscriber, string $aPayload): void
    {
        $this->messageFactory(
            "Message dispatched to " . $this->getShortClassName($subscriber::class)
        )->outputLn();

        $subscriber->handleEvent($this->getSerializer()->unserialize($aPayload));
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // PUBLICAÇÃO
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    /** @override */
    public function publish(string $channel, Event $event): self
    {
        $socketClient = $this->createClient();

        $this->setActivityFor($socketClient, $channel, $event);

        if ($this->isConsole() === true) {
            $this->messageFactory(
                "Publish event of type '" . $this->getShortClassName($event::class) . "'" .
                " to channel '$channel' in " . $this->getAddressString()
            )->successLn();
        }

        return $this;
    }

    /** @param resource $socketClient */
    protected function setActivityFor($socketClient, string $channel, Event $event): void
    {
        $eventContents = ($event instanceof EventSignal)
            ? $event->signal()
            : $this->getSerializer()->serialize($event);

        $payload = $channel
            . $this->separator
            . $event::class
            . $this->separator
            . $eventContents . PHP_EOL;

        fwrite($socketClient, $payload);
        fclose($socketClient);
    }
}
