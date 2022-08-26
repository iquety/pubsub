# Publishing a stop sign

[◂ Publishing an event](09-publishing-an-event.md) | [Documentation Summary](index.md) | [Improving the library ▸](11-improving-library.md)
-- | -- | --

When "Message Broker" is being used in a production environment, it needs to be kept running uninterrupted. How to achieve this has already been explained in [Keeping the "Message Broker" always running](06-keeping-running.md).

It has been said that ["Supervisor"](http://supervisord.org/introduction.html) can be monitoring the "Message Broker" so that if it stops running it can be restarted automatically.

It is possible to send a "Signal Event" to the "Message Broker", to force its stop. This is very useful when new Subscribers are added to the "Message Broker", as the new setting will only take effect when it is restarted.

Here's an example of a signal event:

```php
$publisher = new PhpEventPublisher('localhost', 7703);

$event = new EventSignal(Signals::STOP);

$publisher->publish('channel-vormir', $event);
```

[◂ Publishing an event](09-publishing-an-event.md) | [Documentation Summary](index.md) | [Improving the library ▸](11-improving-library.md)
-- | -- | --
