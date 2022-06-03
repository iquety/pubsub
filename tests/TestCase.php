<?php

declare(strict_types=1);

namespace Tests;

use Closure;
use DateTimeImmutable;
use Freep\PubSub\Event\Event;
use Tests\Example\Events\EventOne;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Freep\Security\Filesystem;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

class TestCase extends FrameworkTestCase
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
        return new Filesystem(__DIR__ . '/files');
    }

    protected function gotcha(Closure $callback): string
    {
        ob_start();
        $callback();
        return (string)ob_get_clean();
    }

    public static function clearFile(string $file): void
    {
        (new Filesystem(__DIR__ . '/files'))
            ->setFileContents($file, '');
    }

    /** @param array<int,string> $messageList */
    protected function assertStringHasMessages(array $messageList, string $output): void
    {
        foreach ($messageList as $message) {
            $this->assertStringContainsString($message, $output);
        }
    }

    /** @param array<int,string> $messageList */
    protected function assertStringNotHasMessages(array $messageList, string $output): void
    {
        foreach ($messageList as $message) {
            $this->assertStringNotContainsString($message, $output);
        }
    }
}
