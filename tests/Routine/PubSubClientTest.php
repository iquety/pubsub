<?php

declare(strict_types=1);

namespace Tests\Routine;

use Iquety\Console\Routine;
use Iquety\Console\PhpUnit\ConsoleTestCase;
use Iquety\Console\Terminal;
use Iquety\PubSub\Routine\PubSubClientRoutine;

class PubSubClientTest extends ConsoleTestCase
{
    private function factory(): Routine
    {
        $terminal = new Terminal(__DIR__);

        $routine =  new PubSubClientRoutine($terminal);

        $routine->enabletestMode();

        return $routine;
    }

    /** @test */
    public function commandInfo(): void
    {
        $routine = $this->factory();

        $this->assertRoutineHasName('pubsub:client-test', $routine);

        $this->assertRoutineHasDescription(
            'Send a test events to the pub/sub message broker',
            $routine
        );

        $this->assertRoutineHasHowToUse(
            './broker pubsub:client-test [options]',
            $routine
        );
    }

    /** @test */
    public function commandOptions(): void
    {
        $routine = $this->factory();
        $options = $routine->getOptions();

        $this->assertCountRoutineOptions(4, $routine);

        $this->assertRoutineHasOption('-h', $routine);
        $this->assertRoutineHasOption('--help', $routine);
        $this->assertOptionIsBoolean($options[0]);
        $this->assertOptionHasDefaultValue('', $options[0]);

        $this->assertRoutineHasOption('-v', $routine);
        $this->assertRoutineHasOption('--verbose', $routine);
        $this->assertOptionHasShortNotation('-v', $options[1]);
        $this->assertOptionIsNotRequired($options[1]);
        $this->assertOptionIsBoolean($options[1]);
        $this->assertOptionHasDefaultValue('', $options[1]);

        $this->assertRoutineHasOption('-d', $routine);
        $this->assertRoutineHasOption('--domain', $routine);
        $this->assertOptionHasShortNotation('-d', $options[2]);
        $this->assertOptionIsNotRequired($options[2]);
        $this->assertOptionIsValued($options[2]);
        $this->assertOptionHasDefaultValue('localhost', $options[2]);

        $this->assertRoutineHasOption('-p', $routine);
        $this->assertRoutineHasOption('--port', $routine);
        $this->assertOptionHasShortNotation('-p', $options[3]);
        $this->assertOptionIsNotRequired($options[3]);
        $this->assertOptionIsValued($options[3]);
        $this->assertOptionHasDefaultValue('7703', $options[3]);
    }

    /** @test */
    public function enableVerboseMode(): void
    {
        $this->expectOutputRegex("/Verbose mode enabled/");

        $routine = $this->factory();
        $routine->run(['-v']);
    }
}
