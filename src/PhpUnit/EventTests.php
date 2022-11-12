<?php

declare(strict_types=1);

namespace Iquety\PubSub\PhpUnit;

use Iquety\PubSub\Event\Event;

trait EventTests
{
    public static function assertIsValidEvent(Event $event, $message = '')
    {
        self::assertThat($event, self::isValidEvent(), $message);
    }

    public static function assertIsNotValidEvent(Event $event, $message = '')
    {
        self::assertThat($event, self::logicalNot(self::isValidEvent()), $message);
    }

    public static function isValidEvent()
    {
        return new IsValidEventConstraint();
    }
}
