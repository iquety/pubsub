<?php

declare(strict_types=1);

namespace Tests\Example\Events;

class Family
{
    public function __construct(
        private string $name,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'name'     => $this->name(),
        ];
    }
}
