<?php

declare(strict_types=1);

namespace Tests\Event;

use DateTimeImmutable;
use Iquety\PubSub\Event\Signal;
use Iquety\PubSub\Event\StopSignal;
use Tests\TestCase;

class SignalTest extends TestCase
{
    /** @test */
    public function useGetters(): void
    {
        $event = new StopSignal();

        $this->assertEquals(Signal::STOP, $event->label());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->occurredOn());
        $this->assertTrue($event->equalTo(new StopSignal()));
        $this->assertFalse($event->equalTo($this->eventOneFactory()));

        $this->assertEquals([], $event->toArray());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function useFactory(): void
    {
        $event = StopSignal::factory([
            'context' => []
        ]);

        $this->assertEquals(Signal::STOP, $event->label());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->occurredOn());
        $this->assertTrue($event->equalTo(new StopSignal()));
        $this->assertFalse($event->equalTo($this->eventOneFactory()));

        $this->assertEquals([], $event->toArray());
    }
}
