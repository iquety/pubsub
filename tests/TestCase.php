<?php

declare(strict_types=1);

namespace Tests;

use Closure;
use DateTimeImmutable;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Publisher\EventPublisher;
use Freep\PubSub\Publisher\PhpEventPublisher;
use Tests\Example\Events\EventOne;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Freep\Security\Filesystem;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

class TestCase extends FrameworkTestCase
{
    protected function eventPublisherFactory(string $className): EventPublisher
    {
        if ($className === SimpleEventPublisher::class) {
            return SimpleEventPublisher::instance()
                ->reset()
                ->enableVerboseMode()
                ->subscribe('channel-one', SubscriberOne::class)
                ->subscribe('channel-one', SubscriberTwo::class)
                ->subscribe('channel-two', SubscriberTwo::class);
        }

        return (new PhpEventPublisher('localhost', 8080))
            ->enableVerboseMode()
            ->subscribe('channel-one', SubscriberOne::class)
            ->subscribe('channel-one', SubscriberTwo::class)
            ->subscribe('channel-two', SubscriberTwo::class);
    }

    protected function emptyEventPublisherFactory(string $className): EventPublisher
    {
        if ($className === SimpleEventPublisher::class) {
            return SimpleEventPublisher::instance()
                ->reset()
                ->enableVerboseMode();
        }

        return (new PhpEventPublisher('localhost', 8080))
            ->enableVerboseMode();
    }

    protected function eventFactory(string $name, string $cpf, string $className = EventOne::class): Event
    {
        $ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
        return new $className($name, $cpf, $ocurredOn);
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
