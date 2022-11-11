<?php

declare(strict_types=1);

namespace Iquety\PubSub\Event;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionObject;

abstract class Event
{
    protected ?DateTimeImmutable $occurredOn = null;

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        $className = get_called_class();

        $values = self::assertImutability($values);

        $arguments = self::makeConstructorArguments($className, $values);

        $event = new $className(...$arguments);

        $occurredOn = isset($values['occurredOn']) === false
            ? new DateTimeImmutable()
            : $values['occurredOn'];

        $reflection = new ReflectionObject($event);
        $property = $reflection->getProperty('occurredOn');
        $property->setAccessible(true);
        $property->setValue($event, $occurredOn);

        return $event;
    }

    abstract public function label(): string;

    public function occurredOn(): DateTimeImmutable
    {
        if ($this->occurredOn === null) {
            $this->occurredOn = new DateTimeImmutable();
        }

        return $this->occurredOn;
    }

    public function sameEventAs(Event $other): bool
    {
        $className = get_called_class();

        return $other instanceof $className
            && $this->toArray() == $other->toArray();
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        $reflection = new ReflectionObject($this);

        $argumentList = $reflection->getConstructor()->getParameters();

        $propertyList = [];

        foreach($argumentList as $argument) {
            $label = $argument->getName();

            $property = $reflection->getProperty($label);
            $property->setAccessible(true);
            
            $value = $property->getValue($this);

            $propertyList[$label] = $value;
        }

        $propertyList['occurredOn'] = $this->occurredOn();

        self::assertImutability($propertyList);

        return $propertyList;
    }

    private static function assertImutability(array $valueList): array
    {
        foreach ($valueList as $name => $value) {
            if ($value instanceof DateTime) {
                throw new InvalidArgumentException('Only immutable dates are allowed');
            }
        }

        return $valueList;
    }

    private static function makeConstructorArguments(string $className, array $values): array
    {
        $reflection = new ReflectionClass($className);
        
        $argumentList = $reflection->getConstructor()->getParameters();

        $list = [];

        foreach($argumentList as $argument) {
            $label = $argument->getName();

            $list[] = $values[$label];
        }

        return $list;
    }
}
