<?php

declare(strict_types=1);

namespace Freep\PubSub\Command;

use DateTimeImmutable;
use Freep\Console\Arguments;
use Freep\Console\Command;
use Freep\Console\Option;
use Freep\PubSub\EventLoop;
use Freep\PubSub\Publisher\SimpleEventPublisher;
use RuntimeException;
use Tests\Example\Events\EventOne;
use Tests\Example\Events\EventTwo;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

class PubSubClientCommand extends Command
{
    protected function initialize(): void
    {
        $this->setName("pubsub:client-test");
        $this->setDescription("Send a test events to the publish/subscribe server");
        $this->setHowToUse("./example pubsub:client-test [options]");

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

        $ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
        $eventOne = new EventOne('ricardo', '99988877766', $ocurredOn);
        $eventTwo = new EventTwo('milene', '88877766655', $ocurredOn);

        try {
            // Ao mandar dois eventos diferentes,
            // o SubscriberOne recede apenas o DummyEvent
            // o SubscriberTwo recede os dois: DummyEvent e FooEvent

            // vai para SubscriberOne e SubscriberTwo
            // ambos estão aptos para receber EventOne
            $publisher->publish('channel-vormir', $eventOne);

            // vai para SubscriberOne e SubscriberTwo
            // apenas SubscriberTwo pode receber EventTwo
            $publisher->publish('channel-vormir', $eventTwo);

            // vai para SubscriberTwo
            // SubscriberTwo pode receber EventTwo
            $publisher->publish('channel-mordor', $eventTwo);

            // não vai pra ninguém. canal sem nenhum inscrito
            $publisher->publish('channel-greenville', $eventTwo);

            // parar o servidor de publish/subscribe
            // $publisher->publish('vormir', new EventSignal(Signals::STOP));

        } catch(RuntimeException $exception) {
            $this->error($exception->getMessage());
        }

        $this->info('Published Events');
        $this->info('Used memory: ' . memory_get_usage());
    }
}
