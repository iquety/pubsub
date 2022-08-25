# Using the example script

--page-nav--

## 1. The "example" script

This repository comes with two terminal commands, implemented with the library [Freep\Console](https://github.com/ricardopedias/freep-console). The commands can be found in the 'src/Command' directory and are invoked by the "example" script located at the root of the repository.

Run the script with the '--help' option to learn how to use it:

```bash
./example --help
```

Something like the text below will be displayed:

```bash
Options: 
-h, --help            Display help information

Available commands: 
help                  Display help information
pubsub:broker         Start the pubsub message broker
pubsub:client-test    Send a test events to the pub/sub message broker
```

## 2. Running "Message Broker"

In ["Implementing in 'Message Broker'"](03-implementing-in-broker.md) it was explained, in a quick way, how to run the "Message Broker". Here, we'll get into the more minute details of the command.

To bring up the "Message Broker", open a terminal and use the following command:

```bash
./example pubsub:broker -d localhost -p 8080 -c 'tests/Example/config-file.php' -s json -v
```

This will run the event server on "localhost" on port "8080". The output should look like the following:

```text
✔ The publish/subscriber server has been started in tcp://localhost:8080
```

For more help information, use the '--help' option with the command name:

```bash
./example pubsub:broker --help
```

The following is a brief explanation of the options used in the command:

Option | Description
-- | --
pubsub:broker | The command executed. Can be "pubsub:broker" or "pubsub:client-test"
-d | The domain where the "Message Broker" should run. Default is 'localhost'
-p | The port for running the "Message Broker". Default is '7703'
-c | The file containing the "Subscribers" configuration
-s | The type of serialization to use. It can be 'php' or 'json'. Default is 'json'
-v | Turn on verbose mode

### (-c) The configuration file

This file must return an anonymous function (Closure) containing the "subscribers" and their respective communication channels. See the example below:

```php
<?php

declare(strict_types=1);

use Freep\PubSub\EventLoop;
use Tests\Example\Subscribers\SubscriberOne;
use Tests\Example\Subscribers\SubscriberTwo;

return function (EventLoop $loop) {
    $loop->addSubscriber('channel-vormir', SubscriberOne::class); // receive EventOne
    $loop->addSubscriber('channel-vormir', SubscriberTwo::class); // receive EventOne e EventTwo
    $loop->addSubscriber('channel-mordor', SubscriberTwo::class); // receive EventOne e EventTwo
};
```

### (-s) The serialization type

Events need to be held before being broadcast to communication channels. There are two types of serialization available: 'php' and 'json'.

> **Important**: For integration of applications developed only in PHP, both types can be used. However, if the system architecture requires modules or subsystems written in different languages (eg Java, Ruby, Python etc), the choice should be for generic 'json' type serialization, which can be easily replicated in any language. .

Example of 'json' serialization:

```text
{"cpf":"123","name":"ricardo","ocurredOn":"2020-01-10 00:00:01"}
```

Example of 'php' serialization:

```text
a:3:{s:3:"cpf";s:3:"123";s:4:"name";s:7:"ricardo";s:9:"ocurredOn";s:19:"2020-01-10 00:00:01";}
```

### (-v) the verbose mode

While the "Message Broker" is running, the current terminal is in watch mode, waiting for events. The '-v' option activates the 'verbose mode' which, at each event received, makes a notification in the open terminal.

## 3. Sending test events

In the same script used to run the "Message Broker", there is a special command to send test events.

With the "Message Broker" running, just open another terminal and use the command below to send some test events.

```bash
./example pubsub:client-test -d localhost -p 8080 -v
```

Test events are sent to three different communication channels: 'channel-vormir', 'channel-mordor' and 'channel-greenville'. Two of these channels have "subscribers" purposely configured from the `tests/Example/config-file.php` file.

For more help information, use the '--help' option with the command name:

```bash
./example pubsub:client-test --help
```

The following is a brief explanation of the options used in the command:

Option | Description
-- | --
pubsub::client-test | The command executed. Can be "pubsub:broker" or "pubsub:client-test"
-d | The domain where the "Message Broker" is running. Default is 'localhost'
-p | The "Message Broker" port. Default is '7703'
-v | Turn on verbose mode

Watch the messages appear on both terminals.

In the **pubsub:client-test** terminal:

```text
✔ Publish event labeled as 'event-one' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event labeled as 'event-two' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event labeled as 'event-two' to channel 'channel-mordor' in tcp://localhost:8080
✔ Publish event labeled as 'event-two' to channel 'channel-greenville' in tcp://localhost:8080
```

In the **pubsub:broker** terminal:

```text
➜ [2022-06-21 17:22:54]: Message labeled as 'event-one' received on channel 'channel-vormir'
Message dispatched to SubscriberOne
Message dispatched to SubscriberTwo

➜ [2022-06-21 17:22:55]: Message labeled as 'event-two' received on channel 'channel-vormir'
Message dispatched to SubscriberTwo

➜ [2022-06-21 17:22:55]: Message labeled as 'event-two' received on channel 'channel-mordor'
Message dispatched to SubscriberTwo

➜ [2022-06-21 17:22:55]: Message labeled as 'event-two' received on channel 'channel-greenville'
There are no subscribers on channel 'channel-greenville'
```

--page-nav--
