<?php

declare(strict_types=1);

namespace Tests\Publisher;

use DateTimeZone;
use Freep\PubSub\Event\Serializer\JsonEventSerializer;
use Freep\PubSub\Event\Serializer\PhpEventSerializer;
use Tests\Publisher\TestCase\SimplePublisherTestCase;

class SimpleEventResetTest extends SimplePublisherTestCase
{
    /** @return array<int,array<string,array>> */
    public function resetableMethodsProvider(): array
    {
        $list = [
            'test mode'    => [
                false,
                'enableTestMode',
                'isTestMode',
                null
            ],
            'verbose mode' => [
                false,
                'enableVerboseMode',
                'isVerboseMode',
                null
            ],
            'timezone'     => [
                new DateTimeZone('UTC'),
                'publishInTimezone',
                'getPublicationTimezone',
                new DateTimeZone('America/Sao_Paulo')
            ],
            'serializer'   => [
                new JsonEventSerializer(),
                'useSerializer',
                'getSerializer',
                new PhpEventSerializer()
            ],
        ];

        return $list;
    }

    /**
     * @test
     * @dataProvider resetableMethodsProvider
     * @param mixed $changedValue
     * @param mixed $defaultValue
     */
    public function reset($defaultValue, string $setterMethod, string $getterMethod, $changedValue): void
    {
        $publisher = $this->simplePublisherFactory([]);

        $this->assertEquals($publisher->{$getterMethod}(), $defaultValue);

        $isBoolean = $changedValue === null;

        if ($isBoolean === true) {
            $publisher->{$setterMethod}();

            $this->assertTrue($publisher->{$getterMethod}());

            $publisher->reset();

            $this->assertEquals($publisher->{$getterMethod}(), $defaultValue);

            return;
        }

        $publisher->{$setterMethod}($changedValue);

        $this->assertEquals($publisher->{$getterMethod}(), $changedValue);

        $publisher->reset();

        $this->assertEquals($publisher->{$getterMethod}(), $defaultValue);
    }
}
