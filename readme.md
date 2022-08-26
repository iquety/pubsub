# Freep Pub/Sub

![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue)
![License](https://img.shields.io/badge/license-MIT-blue)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-pubsub/dashboard?utm_source=github.com&utm_medium=referral&utm_content=ricardopedias/freep-pubsub&utm_campaign=Badge_Coverage)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-pubsub/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ricardopedias/freep-pubsub&amp;utm_campaign=Badge_Grade)

[English](readme.md) | [Português](./docs/pt-br/leiame.md)
-- | --

## Synopsis

This repository contains the necessary functionalities to run a Publish/Subscribe system, making it possible to subscribe and publish events.

For detailed information, see [Documentation Summary](docs/en/index.md).

## How it works

### 1. Run the event server

At the root of this project, there is a terminal command to exemplify the process. It can be used as follows:

```bash
# running in local PHP
./example pubsub:broker -c 'tests/Example/config-file.php' -v

ou

# running in Dockerized PHP
./composer pubsub-server
```

```text
# Server terminal running

➜ Using JsonEventSerializer serializer
➜ Verbose mode enabled
✔ The publish/subscriber server has been started in tcp://localhost:7703
```

Ready! The event server is running on port 7703, which will be available to receive events.

> **Note**: while the server is running, the current terminal will be in "watch" mode, waiting to receive events. The "-v" option activates the "verbose" mode, so that whenever an event is received, the server will notify the running terminal.

### 3. Send events to the server

With the server running, just open another terminal and use the command below to send some test events:

```bash
# running in local PHP
./example pubsub:client-test -d localhost -p 7703 -v

ou

# running in Dockerized PHP
./composer pubsub-client
```

The two terminals will react to the sending of events, showing the corresponding information:

```text
# Terminal where events were sent

✔ Publish event of type 'EventOne' to channel 'channel-vormir' in tcp://localhost:7703
✔ Publish event of type 'EventTwo' to channel 'channel-vormir' in tcp://localhost:7703
✔ Publish event of type 'EventTwo' to channel 'channel-mordor' in tcp://localhost:7703
✔ Publish event of type 'EventTwo' to channel 'channel-greenville' in tcp://localhost:7703
➜ Published Events
➜ Used memory: 1921928
```

```text
# Server terminal that received the events

➜ [2022-06-03 17:06:09]: Message of type 'EventOne' received on channel 'channel-vormir'
Message dispatched to SubscriberOne
Message dispatched to SubscriberTwo

➜ [2022-06-03 17:06:09]: Message of type 'EventTwo' received on channel 'channel-vormir'
Message dispatched to SubscriberTwo

...
```

## Características

- Made for PHP 8.0 or higher;
- Coded with best practices and maximum quality;
- Well documented and IDE friendly;
- Made with TDD (Test Driven Development);
- Implemented with unit tests using PHPUnit;
- Made with :heart: &amp; :coffee:.

## Credits

[Ricardo Pereira Dias](https://www.ricardopedias.com.br)
