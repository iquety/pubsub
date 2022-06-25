<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Freep\Console\Message;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use Freep\PubSub\Event\Serializer\EventSerializer;

abstract class AbstractEventPublisher implements EventPublisher
{
    private ?EventSerializer $serializer = null;

    private int $errorCode = 0;

    private string $errorMessage = '';

    private bool $errorHandlerInit = false;

    private DateTimeZone $publicationTimezone;

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

    public function getPublicationTimezone(): DateTimeZone
    {
        return $this->publicationTimezone;
    }

    protected function getShortClassName(string $classSignature): string
    {
        $typeNodes = explode("\\", $classSignature);
        return array_pop($typeNodes);
    }

    public function publishInTimezone(DateTimeZone $timezone): self
    {
        $this->publicationTimezone = $timezone;
        return $this;
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
        return "[" . date('Y-m-d H:i:s') . "]: ";
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

    protected function convertFromStreamData(array $streamEventData, DateTimeZone $localTimezone): array
    {
        $mapRoutine = function ($value) use ($localTimezone) {
            if (is_string($value) === false) {
                return $value;
            }

            $matches = [];
            $regex = "/([0-9]{4}-[0-9]{2}-[0-9]{2}) ([0-9]{2}:[0-9]{2}:[0-9]{2})/";

            if (preg_match($regex, $value, $matches) !== false && count($matches) === 3) {
                return $this->stringToDateTime($value, $localTimezone);
            }

            return $value;
        };

        return array_map($mapRoutine, $streamEventData);
    }

    protected function convertToStreamData(Event $event, DateTimeZone $publicationTimezone): array
    {
        $realEventData = $event->toArray();

        $mapRoutine = function ($value) use ($publicationTimezone) {
            if ($value instanceof DateTimeImmutable) {
                return $this->dateTimeToString($value, $publicationTimezone);
            }

            return $value;
        };

        return array_map($mapRoutine, $realEventData);
    }

    /** @return string A data no formado Y-m-d H:i:s e convertida em UTC */
    private function dateTimeToString(DateTimeImmutable $value, DateTimeZone $publisherTimezone): string
    {
        // Os timezones padrões são UTC, tanto para o EventPublisher
        // como para os objetos DateTimeImmutable

        if ($publisherTimezone->getName() !== 'UTC') {
            $valueDatetime = $value->format('Y-m-d H:i:s');

            // cria um novo objeto, determinando o fuso horário
            $value = new DateTime($valueDatetime, $publisherTimezone);

            // converte para o fuso UTC.
            // isso universaliza o transporte do evento até o Subscriber.
            $value->setTimezone(new DateTimeZone('UTC'));
        }

        return $value->format('Y-m-d H:i:s');
    }

    /** @return DateTimeImmutable A data convertida para o fuso horário do Subscriber */
    private function stringToDateTime(string $value, DateTimeZone $subscriberTimezone): DateTimeImmutable
    {
        $objectDateTime = new DateTimeImmutable($value);

        // Os timezones padrões são UTC, tanto para o EventPublisher
        // como para os objetos DateTimeImmutable

        if ($subscriberTimezone->getName() !== 'UTC') {
            // o Subscriber irá receber o evento em seu próprio fuso horario
            $objectDateTime = $objectDateTime->setTimezone($subscriberTimezone);
        }

        return $objectDateTime;
    }
}
