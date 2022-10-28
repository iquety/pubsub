<?php

declare(strict_types=1);

namespace Tests\Publisher\TestCase;

use DateTimeImmutable;
use Iquety\PubSub\Publisher\SimpleEventPublisher;
use Tests\Example\Events\EventOne;
use Tests\Example\Events\EventTwo;
use Tests\TestCase;

class PublisherTestCase extends TestCase
{
    public function setUp(): void
    {
        $this->clearLastHandleFile('subscriber-one-handle.txt');
        $this->clearLastHandleFile('subscriber-two-handle.txt');
        $this->clearLastHandleFile('subscriber-three-handle.txt');
    }

    public static function tearDownAfterClass(): void
    {
        self::clearFile('subscriber-one-handle.txt');
        self::clearFile('subscriber-two-handle.txt');
        self::clearFile('subscriber-three-handle.txt');
    }

    /** @return array<string,mixed> */
    protected function readLastEventFromFile(string $file): array
    {
        $parts = explode(
            PHP_EOL . PHP_EOL,
            $this->filesystemFactory()->getFileContents($file)
        );

        return [
            'channel' => $parts[0],
            'label'    => $parts[1],
            'eventData' => trim($parts[2])
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
