<?php

declare(strict_types=1);

namespace Tests\Event;

use DateTimeImmutable;
use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Signals;
use Tests\Example\Events\EventOne;
use PHPUnit\Framework\TestCase;

class EventSignalTest extends TestCase
{
    /** @test */
    public function useGetters(): void
    {
        $event = new EventSignal(Signals::STOP);

        $this->assertEquals(Signals::STOP, $event->signal());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->ocurredOn());
        $this->assertTrue($event->sameEventAs(new EventSignal(Signals::STOP)));
        $this->assertFalse($event->sameEventAs(
            new EventOne('ricardo', '123', new DateTimeImmutable())
        ));

        $this->assertEquals([], $event->toArray());
    }
}
