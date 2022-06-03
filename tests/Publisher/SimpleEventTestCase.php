<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Closure;
use DateTimeImmutable;
use Freep\PubSub\Event\Event;
use Tests\Example\Events\EventOne;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Freep\Security\Filesystem;
use Tests\TestCase;

class SimpleEventTestCase extends TestCase
{
    /** @return array<string,mixed> */
    protected function readLastEventFromFile(string $file): array
    {
        $parts = explode(
            PHP_EOL . PHP_EOL,
            $this->filesystemFactory()->getFileContents($file)
        );

        return [
            'channel' => $parts[0],
            'type'    => $parts[1],
            'payload' => trim($parts[2])
        ];
    }

    protected function clearLastEventFile(string $file): void
    {
        self::clearFile($file);
    }

    protected function readLastHandleFromFile(string $file): string
    {
        return $this->filesystemFactory()->getFileContents($file);
    }

    protected function clearLastHandleFile(string $file): void
    {
        self::clearFile($file);
    }
}
