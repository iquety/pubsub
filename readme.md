# Freep Publish/Subscribe

![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue)
![License](https://img.shields.io/badge/license-MIT-blue)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-pubsub/dashboard?utm_source=github.com&utm_medium=referral&utm_content=ricardopedias/freep-pubsub&utm_campaign=Badge_Coverage)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5a911e53f0cc421282d847d323f50203)](https://www.codacy.com/gh/ricardopedias/freep-pubsub/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ricardopedias/freep-pubsub&amp;utm_campaign=Badge_Grade)

[English](readme.md) | [Português](./docs/pt-br/leiame.md)
-- | --

## Synopsis

This repository contains the functionality needed to run a Publish/Subscribe system, enabling registration and publication of events.

For detailed information, see [Documentation Summary](docs/en/index.md).

## How to use

### 1. Run the event server

At the root of this project, there is a script to exemplify the process.
It can be used as follows:

```bash
./example pubsub -d localhost -p 8080 -t -v
```

```text
# Server terminal running

✔ The publish/subscriber server has been started in tcp://localhost:8080
```

This will run the server event server on port 8080, which will be available to receive messages.

> Note: while the server is running, the current terminal will be in watch mode, waiting for events. The '-v' option activates the 'verbose' mode, so that whenever an event is received, the server will notify the running terminal.

### 3. Send events to the server

With the server running, just open another terminal and use the command below to send some test events.
Watch the messages appear on both terminals.

```bash
./example pubsub:client-test -d localhost -p 8080 -v
```

```text
# Sending terminal

✔ Publish event of type 'EventOne' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-vormir' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-mordor' in tcp://localhost:8080
✔ Publish event of type 'EventTwo' to channel 'channel-greenville' in tcp://localhost:8080
➜ Published Events
➜ Used memory: 1921928
```

```text
# Server terminal running

➜ [2022-06-03 17:06:09]: Message of type 'EventOne' received on channel 'channel-vormir'
Message dispatched to SubscriberOne
Message dispatched to SubscriberTwo

➜ [2022-06-03 17:06:09]: Message of type 'EventTwo' received on channel 'channel-vormir'
Message dispatched to SubscriberTwo

...
```

## Characteristics

- Made for PHP 8.0 or higher;
- Codified with best practices and maximum quality;
- Well documented and IDE friendly;
- Made with TDD (Test Driven Development);
- Implemented with unit tests using PHPUnit;
- Made with :heart: &amp; :coffee:.

## Credits

[Ricardo Pereira Dias](https://www.ricardopedias.com.br)
