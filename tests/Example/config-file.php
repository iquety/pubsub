<?php

declare(strict_types=1);

use Iquety\PubSub\EventLoop;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

return function (EventLoop $loop) {

    $loop->addSubscriber('channel-vormir', SubscriberOne::class); // recebe EventOne
    $loop->addSubscriber('channel-vormir', SubscriberTwo::class); // recebe EventOne e EventTwo
    $loop->addSubscriber('channel-mordor', SubscriberTwo::class); // recebe EventOne e EventTwo
};
