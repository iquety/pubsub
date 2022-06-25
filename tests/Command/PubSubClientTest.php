<?php

declare(strict_types=1);

namespace Tests\Command;

use Freep\Console\Command;
use Freep\Console\PhpUnit\ConsoleTestCase;
use Freep\Console\Terminal;
use Freep\PubSub\Command\PubSubClientCommand;

class PubSubClientTest extends ConsoleTestCase
{
    private function factory(): Command
    {
        $terminal = new Terminal(__DIR__);

        $command =  new PubSubClientCommand($terminal);

        $command->enabletestMode();

        return $command;
    }

    /** @test */
    public function commandInfo(): void
    {
        $command = $this->factory();

        $this->assertCommandHasName('pubsub:client-test', $command);

        $this->assertCommandHasDescription(
            'Send a test events to the pub/sub message broker',
            $command
        );

        $this->assertCommandHasHowToUse(
            './example pubsub:client-test [options]',
            $command
        );
    }

    /** @test */
    public function commandOptions(): void
    {
        $command = $this->factory();
        $options = $command->getOptions();

        $this->assertCountCommandOptions(4, $command);

        $this->assertCommandHasOption('-h', $command);
        $this->assertCommandHasOption('--help', $command);
        $this->assertOptionIsBoolean($options[0]);
        $this->assertOptionHasDefaultValue('', $options[0]);

        $this->assertCommandHasOption('-v', $command);
        $this->assertCommandHasOption('--verbose', $command);
        $this->assertOptionHasShortNotation('-v', $options[1]);
        $this->assertOptionIsNotRequired($options[1]);
        $this->assertOptionIsBoolean($options[1]);
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
        $this->assertOptionHasDefaultValue('8080', $options[3]);
    }

    /** @test */
    public function enableVerboseMode(): void
    {
        $this->expectOutputRegex("/Verbose mode enabled/");

        $command = $this->factory();
        $command->run(['-v']);
    }
}
