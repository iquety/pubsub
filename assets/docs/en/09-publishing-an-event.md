# Publishing an event

--page-nav--

## Introduction

As already explained in [Implementing in the "Starting point" of the application (Bootstrap)](02-implementing-in-bootstrap.md) and also in [Implementing in the Message Broker](03-implementing-in-broker.md), to send events from anywhere on the system, simply invoke the `publish()` method of the appropriate publisher.

There are publishers available for every situation, as will be explained below.

## Publisher for the simple "Observer"

For implementations that start the observer along with the application's bootstrap, the ideal publisher is "SimpleEventPublisher".

See an example below:

```php
$schedule = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new UserRegistered('Ricardo', '99988877766', $schedule);

SimpleEventPublisher::instance()
    ->publish('registrations', $event);
```

In the example above, the "UserRegistered" event is published in the "registrations" channel. The subscriber "RegistrationSubscriber" will handle the event, invoking the appropriate routines for it.

## Publisher to Message Broker

For implementations using "Message Broker", the ideal publisher is "PhpEventPublisher".

See an example below:

```php
$publisher = new PhpEventPublisher('localhost', 7703);

$schedule = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new EventOne('Ricardo', '99988877766', $schedule);

$publisher->publish('channel-vormir', $event);
```

--page-nav--
