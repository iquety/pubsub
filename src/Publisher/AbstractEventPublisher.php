<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use Freep\Console\Message;
use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use Freep\PubSub\Event\Serializer\EventSerializer;

abstract class AbstractEventPublisher implements EventPublisher
{
    private ?EventSerializer $serializer = null;

    private int $errorCode = 0;

    private string $errorMessage = '';

    private bool $verboseMode = false;

    public function enableVerboseMode(): self
    {
        $this->verboseMode = true;
        return $this;
    }

    public function isVerboseMode(): bool
    {
        return $this->verboseMode;
    }

    protected function getShortClassName(string $classSignature): string
    {
        $typeNodes = explode("\\", $classSignature);
        return array_pop($typeNodes);
    }

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

    protected function getErrorCode(): int
    {
        return $this->errorCode;
    }

    protected function hasError(): bool
    {
        return $this->errorMessage !== '';
    }

    protected function setupErrorHandler(): void
    {
        restore_error_handler();

        set_error_handler(function ($errorCode, $errorMessage) {
            $this->errorCode = (int)$errorCode;
            $this->errorMessage = $errorMessage;
            return true;
        });
    }

    // mensagens de terminal

    protected function messageFactory(string $message): Message
    {
        $message = new Message($message);

        if ($this->isVerboseMode() === false) {
            $message->enableQuietMode();
        }

        return $message;
    }
}
