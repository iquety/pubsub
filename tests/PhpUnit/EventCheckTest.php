<?php

declare(strict_types=1);

namespace Tests\PhpUnit;

use DateTime;
use DateTimeImmutable;
use Iquety\PubSub\PhpUnit\EventTests;
use Tests\Event\Support\EventMutable;
use Tests\Event\Support\EventNoConstructor;
use Tests\Event\Support\EventOccurred;
use Tests\TestCase;

class EventCheckTest extends TestCase
{
    use EventTests;

    /** @test */
    public function eventOk(): void
    {
        $event = new EventOccurred(
            'Meu artigo',
            'Um artigo muito legal',
            new DateTimeImmutable()
        );

        $this->assertIsValidEvent($event);
    }

    /** @test */
    public function eventNoConstructor(): void
    {
        $this->assertIsNotValidEvent(new EventNoConstructor());
    }

     /** @test */
    public function eventMutable(): void
    {
        $event = new EventMutable(
            'Meu artigo',
            new DateTime()
        );

        $this->assertIsNotValidEvent($event);
    }
}
