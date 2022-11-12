<?php

declare(strict_types=1);

namespace Iquety\PubSub\PhpUnit;

use InvalidArgumentException;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Event\EventException;
use PHPUnit\Framework\Constraint\Constraint;

class IsValidEventConstraint extends Constraint
{
    public function matches($other): bool
    {
        if (! $other instanceof Event) {
            return false;
        }

        $className = $other::class;

        if ($this->notException($className, $other) === false) {
            return false;
        }

        if ($this->sameEventFactory($className, $other) === false) {
            return false;
        }

        if ($this->anyOrderInProperties($className, $other) === false) {
            return false;
        }

        return true;
    }

    public function toString(): string
    {
        return 'is a valid Event implementation';
    }

    private function notException(string $className, Event $event): bool
    {
        try {
            $event->toArray();
        } catch (EventException) {
            // Every event must have a constructor that receives the state
            return false;
        }

        try {
            $className::factory($event->toArray());
        } catch (EventException) {
            // Only immutable dates are allowed
            // Every event must have a constructor that receives the state
            return false;
        }

        return true;
    }

    private function sameEventFactory(string $className, Event $event): bool
    {
        $newEvent = $className::factory($event->toArray());

        return $newEvent->sameEventAs($event);
    }

    private function anyOrderInProperties(string $className, Event $event): bool
    {
        try {
            $propertyList = $event->toArray();

            $count = count($propertyList);

            for ($x = 0; $x < $count; $x++) {
                // retira o valor começo do array
                $firstKey = key($propertyList);
                $firstValue = array_shift($propertyList);

                // coloca-o no final do array
                $propertyList[$firstKey] = $firstValue;

                // tenta fabricar com a nova ordenação dos argumentos
                $className::factory($propertyList);
            }
        } catch (EventException) {
            // Only immutable dates are allowed
            // Every event must have a constructor that receives the state
            return false;
        }

        return true;
    }
}
