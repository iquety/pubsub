<?php

declare(strict_types=1);

namespace Freep\PubSub\Command;

use Freep\Console\Arguments;
use Freep\Console\Command;
use Freep\Console\Option;
use Freep\PubSub\EventLoop;
use Freep\PubSub\Publisher\PhpEventPublisher;

class PubSubBrokerCommand extends Command
{
    protected function initialize(): void
    {
        $this->setName("pubsub:broker");
        $this->setDescription("Start the pubsub message broker");
        $this->setHowToUse("./example pubsub:broker [options]");

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
                '8080'
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

        if ($arguments->getOption('-v') === '1') {
            $publisher->enableVerboseMode();
        }

        $loop = new EventLoop($publisher);

        $this->resolveConfigFile($loop, $arguments->getOption('-c'));

        $loop->run();
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
            return;
        }

        $callback = include $configFile;
        $callback($loop);
    }
}
