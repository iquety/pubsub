<?php

declare(strict_types=1);

namespace Tests\PhpUnit;

use DateTime;
use DateTimeImmutable;
use Iquety\PubSub\PhpUnit\IsValidEventConstraint;
use PHPUnit\Framework\TestCase;
use Tests\Event\Support\EventArrayIncorrect;
use Tests\Event\Support\EventFactoryIncorrect;
use Tests\Event\Support\EventMutable;
use Tests\Event\Support\EventNoConstructor;
use Tests\Event\Support\EventOccurred;

class IsValidEventConstraintTest extends TestCase
{
    private IsValidEventConstraint $constraint;

    public function setUp(): void
    {
        $this->constraint = new IsValidEventConstraint();
    }

    /** @test */
    public function toStringInvocation(): void
    {
        $this->assertEquals(
            'is a valid Event implementation',
            $this->constraint->toString()
        );
    }

    /** @test */
    public function mutableConstructorArguments(): void
    {
        $this->assertFalse($this->constraint->matches(
            new EventMutable('Titulo', new DateTime())
        ));
    }

    /** @test */
    public function sameEventFactory(): void
    {
        $this->assertFalse($this->constraint->matches(
            new EventArrayIncorrect('Titulo')
        ));
    }

     /** @test */
    public function anyOrderInProperties(): void
    {
        $this->assertFalse($this->constraint->matches(
            new EventFactoryIncorrect('Titulo', new DateTimeImmutable())
        ));
    }

    /** @test */
    public function noConstructorImplementation(): void
    {
        $this->assertFalse($this->constraint->matches(
            new EventNoConstructor()
        ));
    }

    /** @test */
    public function eventOk(): void
    {
        $this->assertTrue($this->constraint->matches(
            new EventOccurred('Título', 'Descrição', new DateTimeImmutable())
        ));
    }
}
