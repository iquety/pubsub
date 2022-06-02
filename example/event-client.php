<?php

// Para executar no terminal
// while true; do ./composer event-client; sleep 2; done;

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

use Freep\PubSub\Event\EventSignal;
use Freep\PubSub\Event\Signals;
use Tests\Example\Events\EventOne;
use Tests\Example\Events\EventTwo;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/factory.php';

$publisher = factoryPublisher();

$ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
$eventOne = new EventOne('ricardo', '99988877766', $ocurredOn);
$eventTwo = new EventTwo('milene', '88877766655', $ocurredOn);

try {
    // Ao mandar dois eventos diferentes,
    // o SubscriberOne recede apenas o DummyEvent
    // o SubscriberTwo recede os dois: DummyEvent e FooEvent

    // vai para SubscriberOne e SubscriberTwo
    // ambos estão aptos para receber EventOne
    $publisher->publish('vormir', $eventOne);

    // vai para SubscriberOne e SubscriberTwo
    // apenas SubscriberTwo pode receber EventTwo
    $publisher->publish('vormir', $eventTwo);

    // vai para SubscriberTwo
    // SubscriberTwo pode receber EventTwo
    $publisher->publish('mordor', $eventTwo);

    // não vai pra ninguém. canal sem nenhum inscrito
    $publisher->publish('greenville', $eventTwo);

    // parar o servidor de publish/subscribe
    // $publisher->publish('vormir', new EventSignal(Signals::STOP));

} catch(RuntimeException $exception) {
    echo 'ERRO: ' . $exception->getMessage() . PHP_EOL;
}

echo 'Eventos Publicados' . PHP_EOL;
echo 'Memoria usada: ' . memory_get_usage() . PHP_EOL;
