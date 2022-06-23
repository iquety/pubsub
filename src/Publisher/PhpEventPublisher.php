<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use DateTimeZone;
use Freep\PubSub\Publisher\PhpEventPublisher\PublisherEngineTrait;
use Freep\PubSub\Publisher\PhpEventPublisher\SubscriberEngineTrait;

/**
 * Publicador de eventos para servidoer baseado em PHP
 * @method PhpEventPublisher subscribe(string $channel, string $subscriberSignatute)
 * @method PhpEventPublisher enableVerboseMode()
 */
class PhpEventPublisher extends SimpleEventPublisher implements EventPublisherLoop
{
    use PublisherEngineTrait;
    use SubscriberEngineTrait;

    /** @var resource|false|null */
    private $customSocket = null;

    private string $host = 'localhost';

    private int $port = 8080;

    private bool $running = true;

    public function __construct(string $host = 'localhost', int $port = 8080)
    {
        $this->host = $host;
        $this->port = $port;
        
        $this->publishInTimezone(new DateTimeZone('UTC'));

        parent::setupErrorHandler();
    }

    protected function getAddressString(): string
    {
        return 'tcp://' . $this->host . ':' . $this->port;
    }

    /** @return resource|false|null */
    protected function getCustomSocket()
    {
        return $this->customSocket;
    }
    
    protected function getHost(): string
    {
        return $this->host;
    }

    protected function getPort(): int
    {
        return $this->port;
    }

    /** @param resource|false|null $socket */
    public function useTestSocket($socket): self
    {
        $this->customSocket = $socket;
        
        $this->enableTestMode();

        return $this;
    }
}
