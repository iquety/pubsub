<?php

declare(strict_types=1);

namespace Tests\Event;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Tests\Event\Support\EventOccurred;
use Tests\TestCase;

class EventTest extends TestCase
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
            'Meu artigo',
            'Um artigo muito legal',
            $dateTime
        );

        $this->assertEquals(
            'Meu artigo',
            $eventOne->toArray()['title']
        );

        $this->assertEquals(
            'Um artigo muito legal',
            $eventOne->toArray()['description']
        );

        $this->assertEquals(
            $dateTime,
            $eventOne->toArray()['schedule']
        );

        $this->assertArrayHasKey('occurredOn', $eventOne->toArray());

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Evento Dois
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $eventTwo = EventOccurred::factory($eventOne->toArray());

        $this->assertEquals(
            'Meu artigo',
            $eventOne->toArray()['title']
        );

        $this->assertEquals(
            'Um artigo muito legal',
            $eventOne->toArray()['description']
        );

        $this->assertEquals(
            $dateTime,
            $eventOne->toArray()['schedule']
        );

        $this->assertEquals($eventOne->occurredOn(), $eventTwo->occurredOn());

        $this->assertTrue($eventTwo->sameEventAs($eventOne));
    }
}
