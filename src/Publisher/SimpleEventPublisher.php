<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use Freep\PubSub\Event\Event;
use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Signals;
use Freep\PubSub\Publisher\EventPublisher;
use RuntimeException;

class SimpleEventPublisher extends PhpEventPublisher implements EventPublisher
{
    /** @var array<string,mixed> */
    private array $config = [];

    private bool $running = true;

    private string $separator = PHP_EOL . PHP_EOL;

    private $customSocket = null;

    private bool $testMode = false;

    public function __construct(string $host = 'localhost', int $port = 8080)
    {
        $this->config['host'] = $host;
        $this->config['port'] = $port;

        parent::setupErrorHandler();
    }

    public function runInTestMode($socket): void
    {
        $this->customSocket = $socket;
        $this->testMode = true;
    }

    /**
     * @return resource
     * @throws RuntimeException
     */
    protected function createClient()
    {
        $address = 'tcp://' . $this->config['host'] . ':' . $this->config['port'];
        $errorNumber  = '';
        $errorMessage = '';

        $socketClient = $this->customSocket !== null 
            ? $this->customSocket
            : stream_socket_client($address, $errorNumber, $errorMessage, 30);

        if ($socketClient === false || $this->hasError() === true) {
            throw new RuntimeException($this->getErrorMessage(), $this->getErrorNumber());
        }

        return $socketClient;
    }

    public function publish(string $channel, Event $event): self
    {
        $socketClient = $this->createClient();

        $this->setActivityFor($socketClient, $channel, $event);

        return $this;
    }

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
    /**
     * @return resource
     * @throws RuntimeException
     */
    protected function createServer()
    {
        $address = 'tcp://' . $this->config['host'] . ':' . $this->config['port'];
        $socketServer = stream_socket_server($address);

        $socketServer = $this->customSocket !== null 
            ? $this->customSocket
            : stream_socket_client($address, $errorNumber, $errorMessage, 30); // @codeCoverageIgnore

        if ($socketServer === false || $this->hasError() === true) {
            throw new RuntimeException($this->getErrorMessage(), $this->getErrorNumber());
        }

        stream_set_blocking($socketServer, false);

        return $socketServer;
    }

    public function consumerLoop(): void
    {
        $socketServer = $this->createServer();

        $this->messageFactory("The publish/subscriber server has been started")->successLn();

        $this->running = true;

        while ($this->running === true) {
            $this->getActivityFrom($socketServer);

            if ($this->testMode === true) {
                $this->running = false;
            }
        }

        fclose($socketServer);

        $this->messageFactory(
            "The publish/subscriber server has been stopped" . PHP_EOL
        )->successLn();
    }

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

        $nowTime = "[" . date('Y-m-d H:i:s') . "]: ";

        $parts = explode($this->separator, $contents); // @phpstan-ignore-line

        if (count($parts) !== 3) {
            $this->messageFactory("The stream received is corrupt")->warningLn();
            return;
        }

        $channel = $parts[0];
        $type    = $this->getShortClassName($parts[1]);
        $payload = trim($parts[2], PHP_EOL);

        $this->messageFactory(
            $nowTime . "Message of type '$type' received on channel '$channel'"
        )->infoLn();

        if ($payload === Signals::STOP) {
            $this->running = false;

            $this->messageFactory(
                $nowTime . "Message to stop the server received"
            )->infoLn();

            return;
        }

        $this->publishToSubscribers($channel, $payload);

        $this->messageFactory('')->outputLn();
    }

    private function getActivityContents($socketServer): string
    {
        if ($this->testMode === true) {
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

        return (int)stream_select($readStream, $writeStream, $exceptions, 0, 200000);
    }
}
