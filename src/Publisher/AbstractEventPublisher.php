<?php

declare(strict_types=1);

namespace Iquety\PubSub\Publisher;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Iquety\Console\Message;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Event\Serializer\JsonEventSerializer;
use Iquety\PubSub\Event\Serializer\EventSerializer;

abstract class AbstractEventPublisher implements EventPublisher
{
    private ?EventSerializer $serializer = null;

    private int $errorCode = 0;

    private string $errorMessage = '';

    private bool $errorHandlerInit = false;

    private bool $testMode = false;

    private bool $verboseMode = false;

    public function isConsole(): bool
    {
        return PHP_SAPI === 'cli' || $this->isTestMode();
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    public function enableTestMode(): self
    {
        $this->testMode = true;
        return $this;
    }

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

    public function reset(): self
    {
        $this->errorCode = 0;
        $this->errorMessage = '';
        $this->publicationTimezone = new DateTimeZone('UTC');
        $this->serializer = $this->getDefaultSerializer();
        $this->testMode = false;
        $this->verboseMode = false;

        return $this;
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

    public function getNowTimeString(): string
    {
        return "[" . date('Y-m-d H:i:s.u') . "]: ";
    }

    public function getSerializer(): EventSerializer
    {
        if ($this->serializer === null) {
            return $this->getDefaultSerializer();
        }

        return $this->serializer;
    }

    // erros

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function hasError(): bool
    {
        return $this->errorMessage !== '';
    }

    protected function setupErrorHandler(): void
    {
        if ($this->errorHandlerInit === true) {
            return;
        }

        restore_error_handler();

        set_error_handler(function ($errorCode, $errorMessage) {
            $this->errorCode = (int)$errorCode;
            $this->errorMessage = $errorMessage;
            return true;
        });

        $this->errorHandlerInit = true;
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
