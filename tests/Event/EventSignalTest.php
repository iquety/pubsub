<?php

declare(strict_types=1);

namespace Tests\Event;

use DateTimeImmutable;
use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Signals;
use Tests\TestCase;

class EventSignalTest extends TestCase
{
    /** @test */
    public function useGetters(): void
    {
        $event = new EventSignal(Signals::STOP);

        $this->assertEquals(Signals::STOP, $event->label());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->ocurredOn());
        $this->assertTrue($event->sameEventAs(new EventSignal(Signals::STOP)));
        $this->assertFalse($event->sameEventAs($this->eventOneFactory()));

        $this->assertEquals([], $event->toArray());
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function useFactory(): void
    {
        $event = EventSignal::factory([
            'signal' => Signals::STOP,
            'context' => []
        ]);

        $this->assertEquals(Signals::STOP, $event->label());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->ocurredOn());
        $this->assertTrue($event->sameEventAs(new EventSignal(Signals::STOP)));
        $this->assertFalse($event->sameEventAs($this->eventOneFactory()));

        $this->assertEquals([], $event->toArray());
    }
}
