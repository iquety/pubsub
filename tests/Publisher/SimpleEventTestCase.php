<?php

declare(strict_types=1);

namespace Tests\Publisher;

use Closure;
use DateTimeImmutable;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Publisher\EventPublisher;
use Tests\Example\Events\EventOne;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Freep\Security\Filesystem;
use PHPUnit\Framework\TestCase;

class SimpleEventTestCase extends TestCase
{
    protected function eventPublisherFactory(): SimpleEventPublisher
    {
        return (new SimpleEventPublisher())
            ->subscribe('channel-one', SubscriberOne::class)
            ->subscribe('channel-one', SubscriberTwo::class)
            ->subscribe('channel-two', SubscriberTwo::class);
    }

    protected function emptyEventPublisherFactory(): SimpleEventPublisher
    {
        return new SimpleEventPublisher();
    }

    protected function eventFactory(string $name, string $cpf): Event
    {
        $ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
        return new EventOne($name, $cpf, $ocurredOn);
    }

    protected function filesystemFactory(): Filesystem
    {
        return new Filesystem(dirname(__DIR__) . '/files');
    }

    protected function gotcha(Closure $callback): string
    {
        ob_start();
        $callback();
        return (string)ob_get_clean();
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
            'type'    => $parts[1],
            'payload' => trim($parts[2])
        ];
    }

    protected static function clearLastEventFile(string $file): void
    {
        (new Filesystem(dirname(__DIR__) . '/files'))
            ->setFileContents($file, '');
    }
}
