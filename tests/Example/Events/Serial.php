<?php

declare(strict_types=1);

namespace Tests\Example\Events;

use JsonSerializable;

class Serial implements JsonSerializable
{
    public function __construct(
        private int $serial
    ) {
    }

    public function serial(): int
    {
        return $this->serial;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'serial' => $this->serial()
        ];
    }

    public function jsonSerialize(): mixed
    {
        return [
            'class' => self::class,
            'state' => $this->toArray()
        ];
    }
}
