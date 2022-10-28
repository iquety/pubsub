<?php

declare(strict_types=1);

namespace Tests\Example\Events;

use DateTimeImmutable;
use Iquety\PubSub\Event\Event;

class EventTwo implements Event
{
    public function __construct(
        private string $name,
        private int $cpf,
        private DateTimeImmutable $ocurredOn
    ) {
    }

    public function label(): string
    {
        return 'event-two';
    }

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        return new self(
            $values['name'],
            $values['cpf'],
            $values['ocurredOn']
        );
    }

    public function cpf(): int
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
            'ocurredOn' => $this->ocurredOn
        ];
    }
}
