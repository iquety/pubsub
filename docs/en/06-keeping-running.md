# Keeping "Message Broker" always running

[◂ Creating a custom script](05-creating-custom-script.md) | [Documentation Summary](index.md) | [Implementing a Subscriber ▸](07-implementing-a-subscriber.md)
-- | -- | --

It is very important that the "Message Broker" keeps running uninterruptedly. However, in very specific cases (an internal problem with the server, hardware, memory, etc.), the watcher may stop and will need to be restarted.

In order not to worry about manually restarting it, it is excellent practice to use a secondary mechanism that keeps checking that the "Message Broker" is "alive".

One suggestion is to make use of ["Supervisor"](http://supervisord.org/introduction.html), a system that allows controlling processes.

With Supervisor installed, just [create a "routine"](http://supervisord.org/running.html#adding-a-program) that restarts the "Message Broker" whenever it stops.

For example:

```ini
[program:restart_broker]
command=/path/to/example pubsub:broker -c 'tests/Example/config-file.php' -v
autostart=true
autorestart=true
redirect_stderr=true
stderr_logfile=/path/to/broker.err.log
stdout_logfile=/path/to/broker.out.log
```

In the "restart_broker" program shown above, all messages (error or escape) will be stored in log files for consultation (broker.err.log and broker.out.log). This is very good to identify what the problem occurred and find a solution more easily.

[◂ Creating a custom script](05-creating-custom-script.md) | [Documentation Summary](index.md) | [Implementing a Subscriber ▸](07-implementing-a-subscriber.md)
-- | -- | --
