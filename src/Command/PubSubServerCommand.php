<?php

declare(strict_types=1);

namespace Freep\PubSub\Command;

use Freep\Console\Arguments;
use Freep\Console\Command;
use Freep\Console\Option;
use Freep\PubSub\EventLoop;
use Freep\PubSub\Publisher\PhpEventPublisher;

class PubSubServerCommand extends Command
{
    protected function initialize(): void
    {
        $this->setName("pubsub");
        $this->setDescription("Start the pubsub server");
        $this->setHowToUse("./example pubsub [options]");

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
                '-t',
                '--test',
                'Runs in test mode, with two subscribers to the "channel-test" channel',
                Option::OPTIONAL
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

        if ($arguments->getOption('-t') === '1') {
            $callback = include dirname(__DIR__, 2) . '/tests/Example/config-file.php';
            $callback($loop);
        }

        $configFile = $arguments->getOption('-c');
        if ($configFile !== null && file_exists($configFile) === true) {
            $callback = include $configFile;
            $callback($loop);
        }

        $loop->run();
    }
}
