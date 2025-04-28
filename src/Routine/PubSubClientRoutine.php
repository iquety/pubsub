<?php

declare(strict_types=1);

namespace Iquety\PubSub\Routine;

use DateTimeImmutable;
use Iquety\Console\Arguments;
use Iquety\Console\Option;
use Iquety\Console\Routine;
use Iquety\PubSub\Publisher\EventPublisher;
use Iquety\PubSub\Publisher\PhpEventPublisher;
use Iquety\PubSub\Publisher\SimpleEventPublisher;
use Iquety\PubSub\Routine\Events\EventOne;
use Iquety\PubSub\Routine\Events\EventTwo;
use RuntimeException;

class PubSubClientRoutine extends Routine
{
    private bool $testMode = false;

    public function enableTestMode(): self
    {
        $this->testMode = true;
        return $this;
    }

    protected function initialize(): void
    {
        $this->setName("pubsub:client-test");
        $this->setDescription("Send a test events to the pub/sub message broker");
        $this->setHowToUse("./broker pubsub:client-test [options]");

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
                '7703'
            )
        );
    }

    protected function handle(Arguments $arguments): void
    {
        $publisher = $this->publisherFactory($arguments);

        if ($arguments->getOption('-v') === '1') {
            $publisher->enableVerboseMode();
            $this->info('Verbose mode enabled');
        }

        $ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
        $eventOne = new EventOne('ricardo', 99988877766, $ocurredOn);
        $eventTwo = new EventTwo('roberto', 88877766655, $ocurredOn);

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
            // $publisher->publish('vormir', new StopSignal());
        } catch (RuntimeException $exception) { // @codeCoverageIgnore
            $this->error($exception->getMessage()); // @codeCoverageIgnore
        }

        $this->info('Published Events');
        $this->info('Used memory: ' . memory_get_usage());
    }

    private function publisherFactory(Arguments $arguments): EventPublisher
    {
        if ($this->testMode === true) {
            return SimpleEventPublisher::instance()->reset();
        }

        // @codeCoverageIgnoreStart
        return new PhpEventPublisher(
            $arguments->getOption('-d'),
            (int)$arguments->getOption('-p')
        );
        // @codeCoverageIgnoreEnd
    }
}
