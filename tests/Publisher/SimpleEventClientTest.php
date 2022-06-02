<?php

declare(strict_types=1);

namespace Tests\Publisher;

use DateTimeImmutable;
use Freep\PubSub\Event\Event;
use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Signals;
use Freep\PubSub\Example\Events\EventOne;
use Freep\PubSub\Example\Subscribers\SubscriberOne;
use Freep\PubSub\Example\Subscribers\SubscriberTwo;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Freep\Security\Filesystem;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SimpleEventClientTest extends TestCase
{
    public static function tearDownAfterClass(): void
    {
        (new Filesystem(dirname(__DIR__) . '/files'))
            ->setFileContents('fake-connection.txt', '');
    }

    private function eventPublisherFactory(): SimpleEventPublisher
    {
        return (new SimpleEventPublisher())
            ->subscribe('channel-one', SubscriberOne::class)
            ->subscribe('channel-one', SubscriberTwo::class)
            ->subscribe('channel-two', SubscriberTwo::class);
    }

    private function eventFactory(string $name, string $cpf): Event
    {
        $ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
        return new EventOne($name, $cpf, $ocurredOn);
    }

    private function filesystemFactory(): Filesystem
    {
        return new Filesystem(dirname(__DIR__) . '/files');
    }

    /** @test */
    public function invalidClient(): void
    {
        $this->expectException(RuntimeException::class);

        $publisher = $this->eventPublisherFactory();
        $publisher->runInTestMode(false);

        $publisher->publish(
            'channel-one',
            $this->eventFactory('ricardo', '123')
        );
    }

    /** @test */
    public function tryConnect(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Unable to connect to tcp/');

        $publisher = $this->eventPublisherFactory();

        $publisher->publish(
            'channel-one',
            $this->eventFactory('ricardo', '123')
        );
    }

    /** @test */
    public function publishEvent(): void
    {
        $publisher = $this->eventPublisherFactory();

        $publisher->runInTestMode(fopen(__DIR__ . '/../files/fake-connection.txt', 'w'));

        $event = $this->eventFactory('ricardo', '123');
        $publisher->publish('channel-one', $event);

        $lastEvent = $this->readLastEvent();
        $this->assertEquals($lastEvent['channel'], 'channel-one');
        $this->assertEquals($lastEvent['type'], EventOne::class);
        $this->assertEquals($lastEvent['payload'], $publisher->getSerializer()->serialize($event));
    }

    /** @test */
    public function publishSignal(): void
    {
        $publisher = $this->eventPublisherFactory();

        $publisher->runInTestMode(fopen(__DIR__ . '/../files/fake-connection.txt', 'w'));

        $event = new EventSignal(Signals::STOP);
        $publisher->publish('channel-one', $event);

        $lastEvent = $this->readLastEvent();
        $this->assertEquals($lastEvent['channel'], 'channel-one');
        $this->assertEquals($lastEvent['type'], EventSignal::class);
        $this->assertEquals($lastEvent['payload'], $event->signal());
    }

    /** @return array<string,mixed> */
    private function readLastEvent(): array
    {
        $parts = explode(
            PHP_EOL . PHP_EOL,
            $this->filesystemFactory()->getFileContents('fake-connection.txt')
        );

        return [
            'channel' => $parts[0],
            'type' => $parts[1],
            'payload' => trim($parts[2])
        ];
    }
}
