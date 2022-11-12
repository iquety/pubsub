# Implementing a Subscriber

[◂ Keeping "Message Broker" always running](06-keeping-running.md) | [Documentation Summary](index.md) | [Implementing an Event ▸](08-implementing-an-event.md)
-- | -- | --

## 1. What is a Subscriber

A Subscriber is responsible for handling the events that occur. It must contain the routine responsible for interpreting an event and knowing what to do when an event of that type happens.

A new Subscriber must implement the `Iquety\PubSub\Subscriber\EventSubscriber` interface, which requires three specific methods:

### 1.1. The "eventFactory" method

This method receives an identification `string` ($eventLabel) and an associative `array` containing the event data ($eventData). Based on this information the "eventFactory" must make the correct event and return it properly on return. If it is not possible to manufacture a suitable event, `null` should be returned:

```php
/** @param array<string,mixed> $eventData */
public function eventFactory(string $eventLabel, array $eventData): ?Event
{
    // hmmm... let's make UserRegistered
    if ($eventLabel === 'user-registered') { 
        return UserRegistered::factory($eventData);
    }

    return null;
}
```

### 1.2. The "handleEvent" method

This method receives an event instance and must invoke the appropriate business rule for it. For example, if it is a registration event, it can invoke some repository or service that performs the appropriate registration.

```php
public function handleEvent(Event $event): void
{
    if ($event instanceof UserRegistered) {
        // ...
        // routine that creates a new user in the database

        return;
    }

    if ($event instanceof UserEmailChanged) {
        // ...
        // routine that updates the email of an existing user in the database
    }
}
```

### 1.3. The "subscribedToEventType" method

This method must return the type of event that the Subscriber is able to handle. Only events of this type will be received in the `handleEvent` method.

```php
public function subscribedToEventType(): string
{
    // Only events of this type will be received by this subscriber
    return UserEvent::class;
}
```

**Important**: Event types can be determined through polymorphism. For example, if `subscribedToEventType` returns type `UserEvent`, all events that implement the `UserEvent` interface will be received in the `handleEvent` method.

Below is an example implementation for the "UserEventSubscriber":

```php
declare(strict_types=1);

namespace Foo\User;

use Foo\User\Events\UserEmailChanged;
use Foo\User\Events\UserRegistered;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Subscriber\EventSubscriber;

class UserEventSubscriber implements EventSubscriber
{
    /** @param array<string,mixed> $eventData */
    public function eventFactory(string $eventLabel, array $eventData): ?Event
    {
        if ($eventLabel === 'user-registered') {
            return UserRegistered::factory($eventData);
        }

        if ($eventLabel === 'user-email-changed') {
            return UserEmailChanged::factory($eventData);
        }

        return null;
    }

    public function handleEvent(Event $event): void
    {
        if ($event instanceof UserRegistered) {
            // ...
            // routine that creates a new user in the database

            return;
        }

        if ($event instanceof UserEmailChanged) {
            // ...
            // routine that updates the email of an existing user in the database
        }
    }

    public function subscribedToEventType(): string
    {
        // Only events of this type will be received by this subscriber
        return UserEvent::class;
    }
}
```

[◂ Keeping "Message Broker" always running](06-keeping-running.md) | [Documentation Summary](index.md) | [Implementing an Event ▸](08-implementing-an-event.md)
-- | -- | --
