# Implementing in the application's "Start point" (Bootstrap)

--page-nav--

## 1. Introduction

This is the simplest way to implement it and consists of starting the "Observer" together with the application through a static call to the 'SimpleEventPublisher' class. In this call, "Subscribers" must be subscribed to be notified when events occur in the system.

## 2. Implementation

The location and implementation of an application's "Start Point" (also called "bootstrap") depends on how it was designed, so there is no standard. In the following example, imagining an application that works with friendly urls, the file 'index.php' would be the "bootstrap", as it is the file invoked to resolve all accesses to the system.

```php
// index.php (file called on every system access)

// ... application start-up routines ...

SimpleEventPublisher::instance()
    ->subscribe('registrations', RegistrationSubscriber::class)
    ->subscribe('payments', PaymentSubscriber::class);

// ... other application start-up routines ...
```

## 2. Sending events

To send events from anywhere on the system, simply invoke the `publish()` method of the publisher:

```php
// any file, somewhere on the system

$ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new UserRegistered('Ricardo', '99988877766', $ocurredOn);

SimpleEventPublisher::instance()
    ->publish('registrations', $event);
```

In the example above, the "UserRegistered" event is published in the "registrations" channel. The subscriber "RegistrationSubscriber" will handle the event, invoking the appropriate routines for it.

--page-nav--
