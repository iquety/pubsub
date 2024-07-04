<?php

declare(strict_types=1);

namespace Tests\Event;

use DateTimeImmutable;
use Tests\Event\Support\EventOccurred;
use Tests\TestCase;

class EventEqualityTest extends TestCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function constructorEquals(): void
    {
        $one = new EventOccurred(
            'Meu artigo',
            'Um artigo muito legal',
            new DateTimeImmutable('2022-10-10 10:10:10.111111'),
        );

        $two = $one;

        $this->assertTrue($one->equalTo($two));
        $this->assertTrue($two->equalTo($one));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function constructorNotEquals(): void
    {
        $one = new EventOccurred(
            'Meu artigo',
            'Um artigo muito legal',
            new DateTimeImmutable('2022-10-10 10:10:10.111111'),
            // ocurredOn automático
        );

        $two = clone $one;

        $thr = new EventOccurred(
            'Meu artigo',
            'Um artigo muito legal',
            new DateTimeImmutable('2022-10-10 10:10:10.111111'),
            // ocurredOn automático
        );

        // data clonada possui microtime diferente
        $this->assertFalse($one->equalTo($two));
        $this->assertFalse($two->equalTo($one));

        // datas construídas setam occurredOn de forma automática
        $this->assertFalse($one->equalTo($thr));
        $this->assertFalse($thr->equalTo($one));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function factoryEquals(): void
    {
        $one = EventOccurred::factory([
            'title' => 'Meu artigo',
            'description' => 'Um artigo muito legal',
            'schedule' => new DateTimeImmutable('2022-10-10 10:10:10.111111'),
            'occurredOn' => new DateTimeImmutable('2022-10-10 10:10:10.111111')
        ]);

        $two = EventOccurred::factory([
            'title' => 'Meu artigo',
            'description' => 'Um artigo muito legal',
            'schedule' => new DateTimeImmutable('2022-10-10 10:10:10.111111'),
            'occurredOn' => new DateTimeImmutable('2022-10-10 10:10:10.111111')
        ]);

        $this->assertTrue($one->equalTo($two));
        $this->assertTrue($two->equalTo($one));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function factoryStringNotEquals(): void
    {
        $one = EventOccurred::factory([
            'title' => 'Meu artigo',
            'description' => 'Um artigo muito legal',
            'schedule' => new DateTimeImmutable('2022-10-10 10:10:10.111111'),
            'occurredOn' => new DateTimeImmutable('2022-10-10 10:10:10.111111')
        ]);

        $two = EventOccurred::factory([
            'title' => 'Meu artigo ', // diferente: tem um espaço no final
            'description' => 'Um artigo muito legal',
            'schedule' => new DateTimeImmutable('2022-10-10 10:10:10.111111'),
            'occurredOn' => new DateTimeImmutable('2022-10-10 10:10:10.111111')
        ]);

        $this->assertFalse($one->equalTo($two));
        $this->assertFalse($two->equalTo($one));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function factoryDateNotEquals(): void
    {
        $one = EventOccurred::factory([
            'title' => 'Meu artigo',
            'description' => 'Um artigo muito legal',
            'schedule' => new DateTimeImmutable('2022-10-10 10:10:10.111111'),
            'occurredOn' => new DateTimeImmutable('2022-10-10 10:10:10.111111')
        ]);

        $two = EventOccurred::factory([
            'title' => 'Meu artigo',
            'description' => 'Um artigo muito legal',
            'schedule' => new DateTimeImmutable('2022-10-10 10:10:10.111112'), // +1 microsegundo
            'occurredOn' => new DateTimeImmutable('2022-10-10 10:10:10.111111')
        ]);

        $this->assertFalse($one->equalTo($two));
        $this->assertFalse($two->equalTo($one));
    }
}
