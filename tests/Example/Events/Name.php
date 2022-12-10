<?php

declare(strict_types=1);

namespace Tests\Example\Events;

class Name
{
    public function __construct(
        private string $name,
        private Family $lastName,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function lastName(): Family
    {
        return $this->lastName;
    }

    public function toArray(): array
    {
        return [
            'name'     => $this->name(),
            'lastName' => $this->lastName()
        ];
    }
}
