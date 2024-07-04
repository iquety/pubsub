<?php

declare(strict_types=1);

namespace Iquety\PubSub\PhpUnit;

use InvalidArgumentException;
use Iquety\PubSub\Event\Event;
use Iquety\PubSub\Event\EventException;
use PHPUnit\Framework\Constraint\Constraint;
use Throwable;

class IsValidEventConstraint extends Constraint
{
    /** @param Event $other */
    public function matches($other): bool
    {
        $className = $other::class;

        if ($this->hasConstructor($other) === false) {
            return false;
        }

        if ($this->anyOrderInProperties($className, $other) === false) {
            return false;
        }

        if ($this->sameEventFactory($className, $other) === false) {
            return false;
        }

        return true;
    }

    public function toString(): string
    {
        return 'is a valid Event implementation';
    }

    private function hasConstructor(Event $event): bool
    {
        try {
            $event->toArray();
        } catch (Throwable) {
            // Every event must have a constructor that receives the state
            return false;
        }

        return true;
    }

    private function sameEventFactory(string $className, Event $event): bool
    {
        $newEvent = $className::factory($event->toArray());

        return $newEvent->equalTo($event);
    }

    private function anyOrderInProperties(string $className, Event $event): bool
    {
        try {
            $propertyList = $event->toArray();

            $className::factory($propertyList);

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
        } catch (Throwable) {
            // Only immutable dates are allowed
            // Every event must have a constructor that receives the state
            return false;
        }

        return true;
    }
}
