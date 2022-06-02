<?php

declare(strict_types=1);

use Freep\PubSub\Publisher\SimpleEventPublisher;

ini_set('display_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

function factoryPublisher()
{
    return new SimpleEventPublisher('localhost', 8080);
}

// function factoryPublisher()
// {
//     return new RedisEventPublisher('freep-pubsub-redis', 6379, 'devel', 'devel');
// }
