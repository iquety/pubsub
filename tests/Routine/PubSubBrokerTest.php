<?php

declare(strict_types=1);

namespace Tests\Routine;

use Iquety\Console\PhpUnit\ConsoleTestCase;
use Iquety\Console\Terminal;
use Iquety\PubSub\Routine\PubSubBrokerRoutine;

class PubSubBrokerTest extends ConsoleTestCase
{
    private function factory(): PubSubBrokerRoutine
    {
        $terminal = new Terminal(__DIR__);

        $routine =  new PubSubBrokerRoutine($terminal);

        $routine->enabletestMode();

        return $routine;
    }

    /** @test */
    public function commandInfo(): void
    {
        $routine = $this->factory();

        $this->assertRoutineHasName('pubsub:broker', $routine);

        $this->assertRoutineHasDescription(
            'Start the pubsub message broker',
            $routine
        );

        $this->assertRoutineHasHowToUse(
            './example pubsub:broker [options]',
            $routine
        );
    }

    /** @test */
    public function commandOptions(): void
    {
        $routine = $this->factory();
        $options = $routine->getOptions();

        $this->assertCountRoutineOptions(6, $routine);

        $this->assertRoutineHasOption('-h', $routine);
        $this->assertRoutineHasOption('--help', $routine);
        $this->assertOptionIsBoolean($options[0]);
        $this->assertOptionHasDefaultValue('', $options[0]);

        $this->assertRoutineHasOption('-c', $routine);
        $this->assertRoutineHasOption('--config', $routine);
        $this->assertOptionHasShortNotation('-c', $options[1]);
        $this->assertOptionIsNotRequired($options[1]);
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

        $this->assertRoutineHasOption('-s', $routine);
        $this->assertRoutineHasOption('--serialization', $routine);
        $this->assertOptionHasShortNotation('-s', $options[4]);
        $this->assertOptionIsNotRequired($options[4]);
        $this->assertOptionIsValued($options[4]);
        $this->assertOptionHasDefaultValue('json', $options[4]);

        $this->assertRoutineHasOption('-v', $routine);
        $this->assertRoutineHasOption('--verbose', $routine);
        $this->assertOptionHasShortNotation('-v', $options[5]);
        $this->assertOptionIsBoolean($options[5]);
        $this->assertOptionIsNotRequired($options[5]);
        $this->assertOptionHasDefaultValue('', $options[5]);
    }

    /** @test */
    public function configFile(): void
    {
        $this->expectOutputRegex("/include .*\/tests\/Example\/config-file.php/");

        $routine = $this->factory();
        $routine->run(['-c', 'tests/Example/config-file.php']);
    }

    /** @test */
    public function configFileNotExists(): void
    {
        $this->expectOutputRegex("/Configuration file .* not found/");

        $routine = $this->factory();
        $routine->run(['-c', 'blabla.php']);
    }

    /** @test */
    public function changeSerializer(): void
    {
        $this->expectOutputRegex("/Using PhpEventSerializer serializer/");

        $routine = $this->factory();
        $routine->run(['-s', 'php']);
    }

    /** @test */
    public function enableVerboseMode(): void
    {
        $this->expectOutputRegex("/Verbose mode enabled/");

        $routine = $this->factory();
        $routine->run(['-v']);
    }
}
