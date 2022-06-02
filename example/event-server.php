<?php

// Para executar no terminal
// ./composer event-server

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

use Freep\PubSub\EventLoop;
use Freep\PubSub\Example\Subscribers\SubscriberOne;
use Freep\PubSub\Example\Subscribers\SubscriberTwo;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/factory.php';

$loop = new EventLoop(
    factoryPublisher()
);
$loop->addSubscriber('vormir', SubscriberOne::class); // recebe EventOne 
$loop->addSubscriber('vormir', SubscriberTwo::class); // recebe EventOne e EventTwo
$loop->addSubscriber('mordor', SubscriberTwo::class); // recebe EventOne e EventTwo

$loop->run();
