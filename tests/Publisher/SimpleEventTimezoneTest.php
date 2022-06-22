<?php

declare(strict_types=1);

namespace Tests\Publisher;

use DateTimeImmutable;
use DateTimeZone;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberThree;
use Tests\Example\Subscribers\SubscriberTwo;

class SimpleEventTimezoneTest extends PublisherTestCase
{
    /** @test */
    public function defaultToDefault(): void
    {
        $publisherDatetime = new DateTimeImmutable('2022-05-22 17:00:00');
        $event = $this->eventOneFactory($publisherDatetime);

        // recebe em UTC às 17:00:00
        SimpleEventPublisher::instance()
            ->enableTestMode()
            ->subscribe('channel-one', SubscriberThree::class); 

        // envia de UTC às 17:00:00
        SimpleEventPublisher::instance()
            ->publish('channel-one', $event);

        $eventHandle = $this->readLastHandleFromFile('subscriber-three-handle.txt');

        // recebe em UTC às 17:00:00
        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberThree",
            "recebeu: Tests\Example\Events\EventOne",
            "em: 2022-05-22 17:00:00"
        ], $eventHandle);
    }

    /** @test */
    public function defaultToSaoPaulo(): void
    {
        $publisherDatetime = new DateTimeImmutable('2022-05-22 17:00:00');
        $event = $this->eventOneFactory($publisherDatetime);

        // recebe em America/Sao_Paulo às 17:00:00
        SimpleEventPublisher::instance()
            ->enableTestMode()
            ->subscribe('channel-one', SubscriberOne::class); 

        // envia de UTC às 17:00:00
        SimpleEventPublisher::instance()
            ->publish('channel-one', $event);

        $eventHandle = $this->readLastHandleFromFile('subscriber-one-handle.txt');

        // recebe em America/Sao_Paulo às 14:00:00
        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberOne",
            "recebeu: Tests\Example\Events\EventOne",
            "em: 2022-05-22 14:00:00"
        ], $eventHandle);
    }

    /** @test */
    public function saoPauloToDefault(): void
    {
        $publisherDatetime = new DateTimeImmutable('2022-05-22 17:00:00');
        $event = $this->eventOneFactory($publisherDatetime);

        // recebe em UTC às 20:00:00
        SimpleEventPublisher::instance()
            ->enableTestMode()
            ->subscribe('channel-one', SubscriberThree::class); 

        // envia de America/Sao_Paulo às 17:00:00
        SimpleEventPublisher::instance()
            ->publishInTimezone(new DateTimeZone('America/Sao_Paulo'))
            ->publish('channel-one', $event);

        $eventHandle = $this->readLastHandleFromFile('subscriber-three-handle.txt');

        // recebe em UTC às 20:00:00
        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberThree",
            "recebeu: Tests\Example\Events\EventOne",
            "em: 2022-05-22 20:00:00"
        ], $eventHandle);
    }

    /** @test */
    public function saoPauloToNewYork(): void
    {
        $publisherDatetime = new DateTimeImmutable('2022-05-22 17:00:00');
        $event = $this->eventOneFactory($publisherDatetime);

        // recebe em America/New_York às 16:00:00
        SimpleEventPublisher::instance()
            ->enableTestMode()
            ->subscribe('channel-one', SubscriberTwo::class); 

        // envia de America/Sao_Paulo às 17:00:00
        SimpleEventPublisher::instance()
            ->publishInTimezone(new DateTimeZone('America/Sao_Paulo'))
            ->publish('channel-one', $event);

        $eventHandle = $this->readLastHandleFromFile('subscriber-two-handle.txt');

        // recebe em America/New_York às 16:00:00
        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberTwo",
            "recebeu: Tests\Example\Events\EventOne",
            "em: 2022-05-22 16:00:00"
        ], $eventHandle);
    }

    /** @test */
    public function saoPauloToSaoPaulo(): void
    {
        $publisherDatetime = new DateTimeImmutable('2022-05-22 17:00:00');
        $event = $this->eventOneFactory($publisherDatetime);

        // recebe em America/Sao_Paulo às 17:00:00
        SimpleEventPublisher::instance()
            ->enableTestMode()
            ->subscribe('channel-one', SubscriberOne::class); 

        // envia de America/Sao_Paulo às 17:00:00
        SimpleEventPublisher::instance()
            ->publishInTimezone(new DateTimeZone('America/Sao_Paulo'))
            ->publish('channel-one', $event);

        $subscriberOneHandle = $this->readLastHandleFromFile('subscriber-one-handle.txt');

        // recebe em America/Sao_Paulo às 17:00:00
        $this->assertStringHasMessages([
            "Tests\Example\Subscribers\SubscriberOne",
            "recebeu: Tests\Example\Events\EventOne",
            "em: 2022-05-22 17:00:00"
        ], $subscriberOneHandle);
    }
}
