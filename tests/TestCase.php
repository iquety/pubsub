<?php

declare(strict_types=1);

namespace Tests;

use Closure;
use DateTimeImmutable;
use Iquety\Console\Option;
use Iquety\PubSub\Event\Event;
use Tests\Example\Events\EventOne;
use Iquety\Security\Filesystem;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Tests\Example\Events\EventTwo;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

class TestCase extends FrameworkTestCase
{
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

    public static function clearFile(string $file): void
    {
        (new Filesystem(__DIR__ . '/files'))
            ->setFileContents($file, '');
    }

    protected function eventOneFactory(
        ?DateTimeImmutable $ocurredOn = null,
        string $name = 'ricardo'
    ): EventOne {
        return new EventOne(
            $name,
            99988877766,
            $ocurredOn ?? new DateTimeImmutable('2022-05-22 17:00:00')
        );
    }

    protected function eventTwoFactory(
        ?DateTimeImmutable $ocurredOn = null,
        string $name = 'ricardo'
    ): EventTwo {
        return new EventTwo(
            $name,
            55544433322,
            $ocurredOn ?? new DateTimeImmutable('2022-05-22 17:00:00')
        );
    }

    /** @return array<array<string>> */
    public function subscriberListFactory(string $one = '', string $two = ''): array
    {
        $list = [];

        $possibilityList = [
            'channel-one' => SubscriberOne::class,
            'channel-two' => SubscriberTwo::class
        ];

        if ($one !== '') {
            $list[] = [ $one, $possibilityList[$one] ];
        }

        if ($two !== '') {
            $list[] = [ $two, $possibilityList[$two] ];
        }

        return $list;
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
}
