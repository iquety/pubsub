# The Publish/Subscribe pattern

--page-nav--

## 1. Event-driven architecture

The first thing to do when implementing an event-driven architecture is to have a way to non-stop checking the application, waiting for some event to happen. When an event is identified, it must be dispatched to the system's routines or modules that are prepared to handle that type of event.

## 2. The Publish/Subscribe approach

There are several ways to implement an event-driven architecture (Rest APIs, Observers, Messaging Tools, etc).

This library implements a specific **architectural pattern** of event forwarding called Publish/Subscribe.

This architectural pattern makes use of a **design pattern** called ["Observer"](https://refactoring.guru/design-patterns/observer). Briefly, an "Observer" has a list of "subscribers" (like a YouTube channel). Each subscriber has their own needs and receives notifications whenever something of interest happens.

The fictional example below is self-explanatory:

```php
$observer = new Observer();

// subscribes three news listeners to two different channels
$observer->subscribe('channel-name', Ricardo::class);
$observer->subscribe('channel-name', Roberto::class);
$observer->subscribe('channel-other-name', Ronaldo::class);

// publish an event for each listener channel
$observer->publish('channel-name', AssistirNetflix::class);
$observer->publish('channel-other-name', AssistirDisneyPlus::class);
```

Note that, in the code above, the news 'AssistirNetflix' will be received by 'Ricardo' and 'Roberto'. The news 'AssistirDisneyPlus' will only be received by 'Ronaldo'.

Based on the fictional example, we will call listeners "Subscribers" and news "Events".

## 3. Implementation types

There are two ways to implement an "Observer" for Publish/Subscribe, with advantages and disadvantages in each scenario:

Implementation | Pros (2) | Cons (4)
-- | -- | --
At the application's "Start Point" (Bootstrap) | It is ideal for communicating modules within the same application. | Creates a coupling with the bootstrap implementation.
-- | Simple to implement and understand, even for those unfamiliar with Pub/Sub architecture. | Different applications need to redeploy bootstrap configuration
-- | -- | Greater care is needed when adding new subscribers.
-- | -- | Applications made with languages other than PHP (eg Java, Ruby, Python) cannot send events.
**Implementation** | **Pros (4)** | **Cons (2)**
In the "Message Broker" | Ideal for integrating different applications. | It may be more difficult to understand for those unfamiliar with Pub/Sub.
-- | It centralizes the configuration of subscribers in the "Message Broker". | You must run and keep the event server always active to receive and dispatch the events that occur.
-- | It provides the real decoupling between the communicating parts. | --
-- | An application made with a language other than PHP can also send events to communicate. | --

The following is more information about the two types of implementation.

--page-nav--
