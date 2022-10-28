# Implementing an Event

--page-nav--

## 1. What is an Event

An event is the encapsulation of information that represents an action that took place at a certain point in time. Events should always be named in the past tense as they are something that has already happened (eg UserRegistered, PasswordChanged etc). The "consequences" of an event are determined by the subscriber (Subscriber), as explained in [Implementing a Subscriber](07-implementing-a-subscriber.md).

## 2. How to implement an Event

A new event must comply with the `Iquety\PubSub\Event\Event` interface contract and its values must be provided only through the constructor, not being possible to change them after instantiation:

```php
class UserRegistered implements Iquety\PubSub\Event\Event
{
    public function __construct(
        private string $name,
        private string $doc,
        private DateTimeImmutable $ocurredOn
    ) {
    }
}
```

> **Tip**: Getters can be implemented as long as they don't change the current state of the event and only work as data accessors.

The `Iquety\PubSub\Event\Event` interface requires five specific methods:

### 1.1. The "factory" method

This method receives an associative `array` containing the event data ($values). Based on these values, the "factory" should manufacture the event and properly return it on return.

**Important**: The return value must always be an event of the same type, and the impossibility of make a new event must trigger an exception.

**More importantly**: If the implementation of the values of an existing event needs to change, either due to an evolution in the system or a necessary correction, this method should guarantee as much backward compatibility as possible with the data implemented in previous versions. This is necessary to ensure that modules or subsystems that have not yet been updated can continue to send events, even if incomplete.

```php
/** @param array<string,mixed> $values */
public static function factory(array $values): Event
{
    // in the previous version 'doc' was called 'document'
    if (isset($values['document']) === true) {
        $values['doc'] = $values['document'];
    }

    return new self(
        $values['name'],
        $values['doc'],
        new DateTimeImmutable($values['ocurredOn'])
    );
}
```

### 1.2. The "label" method

This method must return a unique textual identification, which names the event clearly and objectively. It must be a declarative name and easily recognizable by humans.

Good examples of identification are 'user_registered' or 'user.registered'.

Bad examples are 'registered', '12345' or 'abst345sd'.

```php
public function label(): string
{
    return 'user.registered';
}
```

### 1.3. The "occurredOn" method

This method should return an instance of `\DateTimeImmutable`, containing the value for the current date and time.

```php
public function ocurredOn(): DateTimeImmutable
{
    return $this->ocurredOn;
}
```

### 1.4. The "sameEventAs" method

This method must compare two instances to determine if they are the same event.

```php
/** @param UserRegistered $other */
public function sameEventAs(Event $other): bool
{
    return $other instanceof UserRegistered
        && $this->name() === $other->name()
        && $this->doc() === $other->doc()
        && $this->ocurredOn() === $other->ocurredOn();
}
```

### 1.4. The "toArray" method

This method should return an associative `array` containing the event values in simple primitive types: `string`, `int`, `float` and `bool`.

```php
public function toArray(): array
{
    return [
        'doc'       => $this->doc,
        'name'      => $this->name,
        'ocurredOn' => $this->ocurredOn->format('Y-m-d H:i:s')
    ];
}
```

Below is an example implementation for the "UserRegistered" event:

```php
declare(strict_types=1);

namespace Foo\User\Events;

use DateTimeImmutable;
use Iquety\PubSub\Event\Event;

class UserRegistered implements Event
{
    public function __construct(
        private string $name,
        private string $doc,
        private DateTimeImmutable $ocurredOn
    ) {
    }

    public function label(): string
    {
        return 'user.registered';
    }

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        // in the previous version 'doc' was called 'document'
        if (isset($values['document']) === true) {
            $values['doc'] = $values['document'];
        }
        
        return new self(
            $values['name'],
            $values['doc'],
            new DateTimeImmutable($values['ocurredOn'])
        );
    }

    public function doc(): string
    {
        return $this->doc;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function ocurredOn(): DateTimeImmutable
    {
        return $this->ocurredOn;
    }

    /** @param UserRegistered $other */
    public function sameEventAs(Event $other): bool
    {
        return $other instanceof EventOne
            && $this->name() === $other->name()
            && $this->doc() === $other->doc()
            && $this->ocurredOn() === $other->ocurredOn();
    }

    public function toArray(): array
    {
        return [
            'doc'       => $this->doc,
            'name'      => $this->name,
            'ocurredOn' => $this->ocurredOn->format('Y-m-d H:i:s')
        ];
    }
}
```

--page-nav--
