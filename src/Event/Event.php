<?php

declare(strict_types=1);

namespace Iquety\PubSub\Event;

use DateTime;
use DateTimeImmutable;
use ReflectionClass;
use ReflectionObject;
use RuntimeException;

abstract class Event
{
    protected ?DateTimeImmutable $occurredOn = null;

    /** @param array<string,mixed> $values */
    public static function factory(array $values): Event
    {
        $className = get_called_class();

        self::assertImutability($values);

        $arguments = self::makeConstructorArguments($className, $values);

        $event = self::makeObject($className, $arguments);

        $occurredOn = isset($values['occurredOn']) === false
            ? new DateTimeImmutable()
            : $values['occurredOn'];

        $reflection = new ReflectionObject($event);
        $property = $reflection->getProperty('occurredOn');
        $property->setAccessible(true);
        $property->setValue($event, $occurredOn);

        return $event;
    }

    abstract public static function label(): string;

    public function occurredOn(): DateTimeImmutable
    {
        if ($this->occurredOn === null) {
            $this->occurredOn = new DateTimeImmutable();
        }

        return $this->occurredOn;
    }

    public function equalTo(Event $other): bool
    {
        $className = get_called_class();

        $sameOccurrece = $this->occurredOn()->format('Y-m-d H:i:s.u')
            === $other->occurredOn()->format('Y-m-d H:i:s.u');

        return $other instanceof $className
            && $this->toArray() === $other->toArray()
            && $sameOccurrece === true;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        $reflection = new ReflectionObject($this);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            throw new EventException('Every event must have a constructor that receives the state');
        }

        $argumentList = $constructor->getParameters();

        $propertyList = [];

        foreach ($argumentList as $argument) {
            $label = $argument->getName();

            $property = $reflection->getProperty($label);
            $property->setAccessible(true);

            $value = ($property->isStatic() === true)
                ? $this::$label()
                : $property->getValue($this);

            $propertyList[$label] = $value;
        }

        $propertyList['occurredOn'] = $this->occurredOn();

        self::assertImutability($propertyList);

        return $propertyList;
    }

    /** @param array<string,mixed> $valueList */
    private static function assertImutability(array $valueList): void
    {
        foreach ($valueList as $value) {
            if ($value instanceof DateTime) {
                throw new EventException('Only immutable dates are allowed');
            }
        }
    }

    /**
     * @param class-string<Event> $className
     * @param array<string,mixed> $valueList
     * @return array<int,mixed>
     */
    private static function makeConstructorArguments(string $className, array $valueList): array
    {
        $reflection = new ReflectionClass($className);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            throw new EventException('Every event must have a constructor that receives the state');
        }

        $argumentList = $constructor ->getParameters();

        $list = [];

        foreach ($argumentList as $argument) {
            $label = $argument->getName();

            if (isset($valueList[$label]) === false) {
                throw new StateException(
                    sprintf('State value "%s" does not exist in event', $label)
                );
            }

            $list[] = $valueList[$label];
        }

        return $list;
    }

    /**
     * @param class-string<Event> $className
     * @param array<int,mixed> $arguments
     */
    private static function makeObject(string $className, array $arguments): Event
    {
        return new $className(...$arguments);
    }
}
