<?php

declare(strict_types=1);

namespace Freep\PubSub\Command;

use Freep\Console\Arguments;
use Freep\Console\Command;
use Freep\Console\Option;
use Freep\PubSub\EventLoop;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

class PubSubServerCommand extends Command
{
    protected function initialize(): void
    {
        $this->setName("pubsub");
        $this->setDescription("Start the pubsub server");
        $this->setHowToUse("./example pubsub [options]");

        $this->addOption(
            new Option(
                '-v',
                '--verbose',
                'Run in verbose mode',
                Option::OPTIONAL
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
    }

    protected function handle(Arguments $arguments): void
    {
        $publisher = new SimpleEventPublisher(
            $arguments->getOption('-d'),
            (int)$arguments->getOption('-p')
        );

        if ($arguments->getOption('-v') === '1') {
            $publisher->enableVerboseMode();
        }

        $loop = new EventLoop($publisher);
        
        if ($arguments->getOption('-t') === '1') {
            $loop->addSubscriber('channel-vormir', SubscriberOne::class); // recebe EventOne 
            $loop->addSubscriber('channel-vormir', SubscriberTwo::class); // recebe EventOne e EventTwo
            $loop->addSubscriber('channel-mordor', SubscriberTwo::class); // recebe EventOne e EventTwo
        }

        $loop->run();
    }
}
