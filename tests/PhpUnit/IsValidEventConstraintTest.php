<?php

declare(strict_types=1);

namespace Tests\PhpUnit;

use DateTime;
use DateTimeImmutable;
use Iquety\PubSub\PhpUnit\IsValidEventConstraint;
use PHPUnit\Framework\TestCase;
use Tests\Event\Support\EventMutable;
use Tests\Event\Support\EventNoConstructor;
use Tests\Event\Support\EventOccurred;

class IsValidEventConstraintTest extends TestCase
{
    private $constraint;

    public function setUp(): void
    {
        $this->constraint = new IsValidEventConstraint();
    }

    /** @test */
    public function mutableConstructorArguments()
    {
        $this->assertFalse($this->constraint->matches(
            new EventMutable('Titulo', new DateTime())
        ));
    }

    /** @test */
    public function noConstructorImplementation()
    {
        $this->assertFalse($this->constraint->matches(
            new EventNoConstructor()
        ));
    }

    /** @test */
    public function eventOk()
    {
        $this->assertTrue($this->constraint->matches(
            new EventOccurred('Título', 'Descrição', new DateTimeImmutable())
        ));
    }
}
