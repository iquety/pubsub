# Implementing an Event

[◂ Implementing a Subscriber](07-implementing-a-subscriber.md) | [Documentation Summary](index.md) | [Publishing an event ▸](09-publishing-an-event.md)
-- | -- | --

## 1. What is an Event

An event is the encapsulation of information that represents an action that took place at a certain point in time. Events should always be named in the past tense as they are something that has already happened (eg UserRegistered, PasswordChanged etc). The "consequences" of an event are determined by the subscriber (Subscriber), as explained in [Implementing a Subscriber](07-implementing-a-subscriber.md).

## 2. How to implement an Event

A new event must fulfill the `Equity\Pub Sub\Event\Event` contract.

The minimum implementation must include the '__constructor' and 'label' methods:

### 2.1. Constructor

All events must receive their values only through the constructor.
It should not be possible to change them after instantiation, in order to guarantee their immutability.

**Important**: Date values must implement `DateTimeImmutable`!

```php
class UserRegistered extends Iquety\PubSub\Event\Event
{
public function __construct(
private string $name,
private string $cpf,
private DateTimeImmutable $schedule
) {
}

// abstract public function label(): string;
}
```

### 2.2. The "label" method

This method must return a unique textual identification, which names the event clearly and objectively.
It must be a declarative name and easily recognizable by humans.

Good examples of identification are 'user_registered' or 'user.registered'.

Bad examples are 'registered', '12345' or 'abst345sd'.

```php
public function label(): string
{
return 'user.registered';
}
```

### 2.3. Getters

Getters can be implemented as long as they don't change the current state of the event and only work as data accessors.

```php
class UserRegistered extends Iquety\PubSub\Event\Event
{
public function __construct(
private string $name,
private string $cpf,
private DateTimeImmutable $schedule
) {
}

public function cpf(): string
{
return $this->cpf;
}

public function name(): string
{
return $this->name;
}
}
```

## 3. Methods available in the event

The abstract class `Iquety\PubSub\Event\Event` provides four specific methods:

### 3.1. The "factory" method

This method receives an associative `array` containing the event data ($values). Based on these values, the "factory" should manufacture the event and properly return it on return. **If an additional value called 'occurredOn' is provided with an instance of `DateTimeImmutable`, the date will be applied to the event that will be created**.

**Important**: The return value must always be an event of the same type, and the impossibility of manufacturing a new event must trigger an exception.

**More importantly**: This method can be overridden to favor event backwards compatibility. If the implementation of the values ​​of an existing event needs to change, either due to an evolution in the system or a necessary correction, this method must guarantee the maximum possible backward compatibility with the data implemented in previous versions. This is necessary to ensure that modules or subsystems that have not yet been updated can continue to send events, even if incomplete.


```php
/** @param array<string,mixed> $values */
public static function factory(array $values): Event
{
// in the previous version 'cpf' was called 'document'
if (isset($values['document']) === true) {
$values['cpf'] = $values['document'];
}

return parent::factory($values);
}
```

### 3.2. The "occurredOn" method

This method returns an instance of `DateTimeImmutable`, containing the value for the current date and time, representing the moment when the event happened.

```php
public function occurredOn(): DateTimeImmutable;
```

### 3.3. The "sameEventAs" method

This method compares two instances to determine if they are the same event.

```php
/** @param UserRegistered $other */
public function sameEventAs(Event $other): bool;
```

### 3.4. O método "toArray"

This method returns an associative `array` containing the event values in simple primitive types: `string`, `int`, `float` and `bool`.
In addition to the arguments passed in the constructor, this method will return an additional value called 'occurredOn' when the event occurs.

```php
public function toArray(): array;
```


## 4. Exemplo

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
private string $cpf,
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
// in the previous version 'cpf' was called 'document'
if (isset($values['document']) === true) {
$values['cpf'] = $values['document'];
}

return new self(
$values['name'],
$values['cpf'],
new DateTimeImmutable($values['ocurredOn'])
);
}

public function cpf(): string
{
return $this->cpf;
}

public function name(): string
{
return $this->name;
}
}
```

[◂ Implementing a Subscriber](07-implementing-a-subscriber.md) | [Documentation Summary](index.md) | [Publishing an event ▸](09-publishing-an-event.md)
-- | -- | --
