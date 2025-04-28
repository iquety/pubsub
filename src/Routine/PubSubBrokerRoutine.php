<?php

declare(strict_types=1);

namespace Iquety\PubSub\Routine;

use Iquety\Console\Arguments;
use Iquety\Console\Routine;
use Iquety\Console\Option;
use Iquety\PubSub\Event\Serializer\PhpEventSerializer;
use Iquety\PubSub\EventLoop;
use Iquety\PubSub\Publisher\PhpEventPublisher;

class PubSubBrokerRoutine extends Routine
{
    private bool $testMode = false;

    public function enableTestMode(): self
    {
        $this->testMode = true;
        return $this;
    }

    protected function initialize(): void
    {
        $this->setName("pubsub:broker");
        $this->setDescription("Start the pubsub message broker");
        $this->setHowToUse("./broker pubsub:broker [options]");

        $this->addOption(
            new Option(
                '-c',
                '--config',
                'Provides a file containing a list of channels and their subscribers',
                Option::OPTIONAL | Option::VALUED,
                ''
            )
        );

        $this->addOption(
            new Option(
                '-d',
                '--domain',
                'Provides the host available for connections',
                Option::OPTIONAL | Option::VALUED,
                'localhost'
            )
        );

        $this->addOption(
            new Option(
                '-p',
                '--port',
                'Provides the port available for connections',
                Option::OPTIONAL | Option::VALUED,
                '7703'
            )
        );

        $this->addOption(
            new Option(
                '-s',
                '--serialization',
                'Specifies which type of serialization to use: php or json',
                Option::OPTIONAL | Option::VALUED,
                'json'
            )
        );

        $this->addOption(
            new Option(
                '-v',
                '--verbose',
                'Run in verbose mode',
                Option::OPTIONAL
            )
        );
    }

    protected function handle(Arguments $arguments): void
    {
        $publisher = new PhpEventPublisher(
            $arguments->getOption('-d'),
            (int)$arguments->getOption('-p')
        );

        $serializerMessage = 'Using JsonEventSerializer serializer';

        if ($arguments->getOption('-s') === 'php') {
            $publisher->useSerializer(new PhpEventSerializer());

            $serializerMessage = 'Using PhpEventSerializer serializer';
        }

        $this->info($serializerMessage);

        if ($arguments->getOption('-v') === '1') {
            $publisher->enableVerboseMode();
            $this->info('Verbose mode enabled');
        }

        $loop = new EventLoop($publisher);

        $this->resolveConfigFile($loop, $arguments->getOption('-c'));

        if ($this->testMode === true) {
            return;
        }

        $loop->run(); // @codeCoverageIgnore
    }

    private function resolveConfigFile(EventLoop $loop, string $configFile): void
    {
        if ($configFile === '') {
            return;
        }

        if (str_starts_with($configFile, DIRECTORY_SEPARATOR) === false) {
            $configFile = getcwd() . DIRECTORY_SEPARATOR . $configFile;
        }

        if (file_exists($configFile) === false) {
            $this->warning("Configuration file '$configFile' not found");
            return;
        }

        if ($this->testMode === true) {
            $this->line('include ' . $configFile);
        }

        $callback = include $configFile;
        $callback($loop);
    }
}
