<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use Freep\Console\Message;
use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use Freep\PubSub\Event\Serializer\EventSerializer;

abstract class AbstractEventPublisher implements EventPublisher
{
    private ?EventSerializer $serializer = null;

    private int $errorNumber = 0;

    private string $errorMessage = '';

    // serializador

    public function useSerializer(EventSerializer $serializer): self
    {
        $this->serializer = $serializer;
        return $this;
    }

    protected function getDefaultSerializer(): EventSerializer
    {
        return new JsonEventSerializer();
    }

    public function getSerializer(): EventSerializer
    {
        if ($this->serializer === null) {
            return $this->getDefaultSerializer();
        }

        return $this->serializer;
    }

    // erros

    protected function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    protected function getErrorNumber(): int
    {
        return $this->errorNumber;
    }

    protected function hasError(): bool
    {
        return $this->errorMessage !== '';
    }

    protected function setupErrorHandler(): void
    {
        restore_error_handler();

        set_error_handler(function ($errorNumber, $errorMessage) {
            $this->errorNumber = (int)$errorNumber;
            $this->errorMessage = $errorMessage;
            return true;
        });
    }

    // mensagens de terminal
    
    protected function messageFactory(string $message): Message
    {
        return new Message($message);
    }
}
