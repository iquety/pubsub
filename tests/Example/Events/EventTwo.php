<?php

declare(strict_types=1);

namespace Tests\Example\Events;

use DateTimeImmutable;
use Freep\PubSub\Event\Event;

class EventTwo implements Event
{
    public function __construct(
        private string $name,
        private string $cpf,
        private DateTimeImmutable $ocurredOn
    ) {
    }

    public static function factory(array $values): Event
    {
        return new self(
            $values['name'],
            $values['cpf'],
            new DateTimeImmutable($values['ocurredOn'])
        );
    }

    public function cpf(): string
    {
        return $this->cpf;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function ocurredOn(): DateTimeImmutable
    {
        return $this->ocurredOn;
    }

    /** @param EventTwo $other */
    public function sameEventAs(Event $other): bool
    {
        return $other instanceof EventTwo
            && $this->name() === $other->name()
            && $this->cpf === $other->cpf()
            && $this->ocurredOn() === $other->ocurredOn();
    }

    public function toArray(): array
    {
        return [
            'cpf'       => $this->cpf,
            'name'      => $this->name,
            'ocurredOn' => $this->ocurredOn->format('Y-m-d H:i:s')
        ];
    }
}
