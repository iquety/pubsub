# Implementing in "Message Broker"

[◂ Implementing in the application's "Start point" (Bootstrap)](02-implementing-in-bootstrap.md) | [Documentation Summary](index.md) | [Using the example script ▸](04-using-script-example.md)
-- | -- | --

## 1. Introduction

This is the most interesting way in most cases. The Publish/Subscribe architecture emerged precisely to promote decoupling in the communication of things that happen in a system.

The goal is for actions to occur without generating dependencies between modules. This format allows for such high decoupling that even languages other than PHP will be able to send events.

This type of approach consists of keeping a "Message Broker" running to receive the events that have occurred. As will be explained next.

### 1. Run the Message Broker

At the root of this repository there is a script called "example", which contains an example implementation using the library [Iquety Console](https://github.com/iquety/console).

> **Note**: For ease of execution, you can implement a custom script similar to this one in your project. See ["Creating a custom script"](05-creating-custom-script.md) for more information.

To run the "Message Broker", open a terminal and type the following command:

```bash
./example pubsub:broker -c 'tests/Example/config-file.php' -v
```

This will run the "Message Broker" on "localhost" on port "7703", with two "Subscribers" and two channels (configured in the `tests/Example/config-file.php` file). More information in [Using the example script](04-using-script-example.md). The output should look like the following:

```text
✔ The publish/subscriber server has been started in tcp://localhost:7703
```

### 2. Sending events through PHP

To send events anywhere in the system, simply invoke the `publish()` method of the "PhpEventPublisher" publisher. See an example below:

```php
$publisher = new PhpEventPublisher('localhost', 7703);

$ocurredOn = new DateTimeImmutable('2020-01-10 00:00:01');
$event = new EventOne('Ricardo', '99988877766', $ocurredOn);

$publisher->publish('channel-vormir', $event);
```

### 3. Sending events by other languages

It is possible to send events from applications built in languages other than PHP. This is achieved by sending a simple TCP message to the running "Message Broker" (in the current case, tcp://localhost:7703).

An example of sending the event "Tests\Example\Events\EventOne" can be seen below:

```text
channel-one

event-one

{"cpf":"123","name":"ricardo","ocurredOn":"2020-01-10 00:00:01"}
```

The message format must follow the following scheme:

Contents | Description
-- | --
channel name | plain text
two line breaks | "\n" + "\n"
event name | Name returned by the Event->label() method of the event
two line breaks | "\n" + "\n"
serialized json content | **Important**: the "Message Broker" must be configured to use Json type serializations (the library's default). More info in [Using the example script](04-using-script-example.md)
a line break | "\n"

[◂ Implementing in the application's "Start point" (Bootstrap)](02-implementing-in-bootstrap.md) | [Documentation Summary](index.md) | [Using the example script ▸](04-using-script-example.md)
-- | -- | --
