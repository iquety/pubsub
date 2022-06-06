<?php

declare(strict_types=1);

namespace Freep\PubSub\Publisher;

interface Loop
{
    public function consumerLoop(): void;
}