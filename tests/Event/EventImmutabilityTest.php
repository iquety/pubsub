<?php

declare(strict_types=1);

namespace Tests\Event;

use DateTime;
use Iquety\PubSub\Event\EventException;
use Tests\Event\Support\EventMutable;
use Tests\TestCase;

class EventImmutabilityTest extends TestCase
{
    /** @test */
    public function constructorWithMutableDate(): void
    {
        $this->expectException(EventException::class);
        $this->expectExceptionMessage('Only immutable dates are allowed');

        $event = new EventMutable('Meu artigo', new DateTime());

        $event->toArray();
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function factoryWithMutableDate(): void
    {
        $this->expectException(EventException::class);
        $this->expectExceptionMessage('Only immutable dates are allowed');

        EventMutable::factory([
            'title' => 'Meu artigo',
            'schedule' => new DateTime()
        ]);
    }
}
