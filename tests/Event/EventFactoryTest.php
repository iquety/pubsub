<?php

declare(strict_types=1);

namespace Tests\Event;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Iquety\PubSub\Event\EventException;
use Iquety\PubSub\Event\StateException;
use Tests\Event\Support\EventNoConstructor;
use Tests\Event\Support\EventOccurred;
use Tests\TestCase;

class EventFactoryTest extends TestCase
{
    /** @return array<string,array<DateTimeImmutable>> */
    public function dateTimeProvider(): array
    {
        return [
            'zone null' => [ new DateTimeImmutable('2022/10/10 10:10:10') ],
            'zone utc' => [
                new DateTimeImmutable('2022/10/10 10:10:10', new DateTimeZone('UTC'))
            ],
            'zone america sao paulo' => [
                new DateTimeImmutable('2022/10/10 10:10:10', new DateTimeZone('America/Sao_Paulo'))
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dateTimeProvider
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function eventFactory(DateTimeImmutable $dateTime): void
    {
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Evento Um
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $eventOne = new EventOccurred(
            'Meu artigo',            // title
            'Um artigo muito legal', // description
            $dateTime                // schedule
        );

        $this->assertEquals('Meu artigo', $eventOne->toArray()['title']);
        $this->assertEquals('Um artigo muito legal', $eventOne->toArray()['description']);
        $this->assertEquals($dateTime, $eventOne->toArray()['schedule']);

        // a propriedade 'occurredOn' é criada automaticamente na construção
        $this->assertArrayHasKey('occurredOn', $eventOne->toArray());

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Evento Dois
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        // a fábrica está recebendo um valor para 'occurredOn'
        $eventTwo = EventOccurred::factory($eventOne->toArray());

        $this->assertEquals('Meu artigo', $eventOne->toArray()['title']);
        $this->assertEquals('Um artigo muito legal', $eventOne->toArray()['description']);
        $this->assertEquals($dateTime, $eventOne->toArray()['schedule']);

        // ambos eventos possuem o mesmo momento de ocorrência
        // pois a fábrica recebeu o valor de 'occurredOn'
        $this->assertEquals($eventOne->occurredOn(), $eventTwo->occurredOn());

        // ambos objetos representam o mesmo evento
        $this->assertTrue($eventTwo->sameEventAs($eventOne));

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Evento Três
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $valueList = $eventOne->toArray();
        unset($valueList['occurredOn']); // remove o valor de 'occurredOn'

        // a fábrica está recebendo apenas os valores do construtor
        $eventTwo = EventOccurred::factory($valueList);

        $this->assertEquals('Meu artigo', $eventOne->toArray()['title']);
        $this->assertEquals('Um artigo muito legal', $eventOne->toArray()['description']);
        $this->assertEquals($dateTime, $eventOne->toArray()['schedule']);

        // os eventos possuem momentos de ocorrência diferentes
        // pois a fábrica NÃO recebeu o valor de 'occurredOn'
        $this->assertNotEquals($eventOne->occurredOn(), $eventTwo->occurredOn());

        // são dois eventos distintos
        $this->assertFalse($eventTwo->sameEventAs($eventOne));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function factoryOk(): void
    {
        $date = new DateTimeImmutable('2022-10-10 10:10:10.777777');

        /** @var EventOccurred $eventOne */
        $eventOne = EventOccurred::factory([
            'title' => 'Meu artigo',
            'description' => 'Um artigo muito legal',
            'schedule' => $date
        ]);

        $this->assertInstanceOf(EventOccurred::class, $eventOne);
        $this->assertEquals('Meu artigo', $eventOne->title());
        $this->assertEquals('Um artigo muito legal', $eventOne->description());
        $this->assertEquals($date->format('Y-m-d H:i:s.u'), $eventOne->schedule()->format('Y-m-d H:i:s.u'));
        $this->assertEquals('2022-10-10 10:10:10.777777', $eventOne->schedule()->format('Y-m-d H:i:s.u'));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function factoryWithInvalidState(): void
    {
        $this->expectException(StateException::class);
        $this->expectExceptionMessage(
            'State value "description" does not exist in event'
        );

        EventOccurred::factory([
            'title' => 'Meu artigo',
            'invalid' => 'Um artigo muito legal', // chave não é um argumento do construtor
            'schedule' => new DateTimeImmutable()
        ]);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function factoryWithoutConstructor(): void
    {
        $this->expectException(EventException::class);
        $this->expectExceptionMessage(
            'Every event must have a constructor that receives the state'
        );

        EventNoConstructor::factory([]);
    }

    /** @test */
    public function toArrayWithoutConstructor(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Every event must have a constructor that receives the state'
        );

        $event = new EventNoConstructor();

        $event->toArray();
    }
}
