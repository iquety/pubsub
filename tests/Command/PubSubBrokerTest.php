<?php

declare(strict_types=1);

namespace Tests\Command;

use Freep\Console\PhpUnit\ConsoleTestCase;
use Freep\Console\Terminal;
use Freep\PubSub\Command\PubSubBrokerCommand;

class PubSubBrokerTest extends ConsoleTestCase
{
    private function factory(): PubSubBrokerCommand
    {
        $terminal = new Terminal(__DIR__);

        $command =  new PubSubBrokerCommand($terminal);

        $command->enabletestMode();

        return $command;
    }

    /** @test */
    public function commandInfo(): void
    {
        $command = $this->factory();

        $this->assertCommandHasName('pubsub:broker', $command);

        $this->assertCommandHasDescription(
            'Start the pubsub message broker',
            $command
        );

        $this->assertCommandHasHowToUse(
            './example pubsub:broker [options]',
            $command
        );
    }

    /** @test */
    public function commandOptions(): void
    {
        $command = $this->factory();
        $options = $command->getOptions();

        $this->assertCountCommandOptions(6, $command);

        $this->assertCommandHasOption('-h', $command);
        $this->assertCommandHasOption('--help', $command);
        $this->assertOptionIsBoolean($options[0]);
        $this->assertOptionHasDefaultValue('', $options[0]);

        $this->assertCommandHasOption('-c', $command);
        $this->assertCommandHasOption('--config', $command);
        $this->assertOptionHasShortNotation('-c', $options[1]);
        $this->assertOptionIsNotRequired($options[1]);
        $this->assertOptionHasDefaultValue('', $options[1]);

        $this->assertCommandHasOption('-d', $command);
        $this->assertCommandHasOption('--domain', $command);
        $this->assertOptionHasShortNotation('-d', $options[2]);
        $this->assertOptionIsNotRequired($options[2]);
        $this->assertOptionIsValued($options[2]);
        $this->assertOptionHasDefaultValue('localhost', $options[2]);

        $this->assertCommandHasOption('-p', $command);
        $this->assertCommandHasOption('--port', $command);
        $this->assertOptionHasShortNotation('-p', $options[3]);
        $this->assertOptionIsNotRequired($options[3]);
        $this->assertOptionIsValued($options[3]);
        $this->assertOptionHasDefaultValue('7703', $options[3]);

        $this->assertCommandHasOption('-s', $command);
        $this->assertCommandHasOption('--serialization', $command);
        $this->assertOptionHasShortNotation('-s', $options[4]);
        $this->assertOptionIsNotRequired($options[4]);
        $this->assertOptionIsValued($options[4]);
        $this->assertOptionHasDefaultValue('json', $options[4]);

        $this->assertCommandHasOption('-v', $command);
        $this->assertCommandHasOption('--verbose', $command);
        $this->assertOptionHasShortNotation('-v', $options[5]);
        $this->assertOptionIsBoolean($options[5]);
        $this->assertOptionIsNotRequired($options[5]);
        $this->assertOptionHasDefaultValue('', $options[5]);
    }

    /** @test */
    public function configFile(): void
    {
        $this->expectOutputRegex("/include .*\/tests\/Example\/config-file.php/");

        $command = $this->factory();
        $command->run(['-c', 'tests/Example/config-file.php']);
    }

    /** @test */
    public function configFileNotExists(): void
    {
        $this->expectOutputRegex("/Configuration file .* not found/");

        $command = $this->factory();
        $command->run(['-c', 'blabla.php']);
    }

    /** @test */
    public function changeSerializer(): void
    {
        $this->expectOutputRegex("/Using PhpEventSerializer serializer/");

        $command = $this->factory();
        $command->run(['-s', 'php']);
    }

    /** @test */
    public function enableVerboseMode(): void
    {
        $this->expectOutputRegex("/Verbose mode enabled/");

        $command = $this->factory();
        $command->run(['-v']);
    }
}
