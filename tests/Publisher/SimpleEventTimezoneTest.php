<?php

declare(strict_types=1);

namespace Tests\Publisher;

use DateTimeImmutable;
use DateTimeZone;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberThree;
use Tests\Example\Subscribers\SubscriberTwo;
use Tests\Publisher\TestCase\SimplePublisherTestCase;

class SimpleEventTimezoneTest extends SimplePublisherTestCase
{
    /** @test */
    public function defaultToDefault(): void
    {
        // cria um evento ocorrido às 17:00:00
        $event = $this->eventOneFactory();

        // SubscriberThree recebe em UTC às 17:00:00
        $publisher = $this->simplePublisherFactory([
            [ 'channel-one', SubscriberThree::class]
        ]);

        // Publisher envia de UTC às 17:00:00
        $publisher->publish('channel-one', $event);

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
        // cria um evento ocorrido às 17:00:00
        $event = $this->eventOneFactory();

        // SubscriberOne recebe em America/Sao_Paulo às 17:00:00
        $publisher = $this->simplePublisherFactory([
            [ 'channel-one', SubscriberOne::class]
        ]);


        // Publisher envia de UTC às 17:00:00
        $publisher->publish('channel-one', $event);

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
        // cria um evento ocorrido às 17:00:00
        $event = $this->eventOneFactory();

        // SubscriberThree recebe em UTC às 20:00:00
        $publisher = $this->simplePublisherFactory([
            [ 'channel-one', SubscriberThree::class]
        ]);

        // Publisher envia de America/Sao_Paulo às 17:00:00
        $publisher
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
        // cria um evento ocorrido às 17:00:00
        $event = $this->eventOneFactory();

        // SubscriberTwo recebe em America/New_York às 16:00:00
        $publisher = $this->simplePublisherFactory([
            [ 'channel-one', SubscriberTwo::class]
        ]);

        // Publisher envia de America/Sao_Paulo às 17:00:00
        $publisher
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
        // cria um evento ocorrido às 17:00:00
        $event = $this->eventOneFactory();

        // SubscriberOne recebe em America/Sao_Paulo às 17:00:00
        $publisher = $this->simplePublisherFactory([
            [ 'channel-one', SubscriberOne::class]
        ]);

        // Publisher envia de America/Sao_Paulo às 17:00:00
        $publisher
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
