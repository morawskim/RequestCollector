<?php

namespace Mmo\RequestCollector;

use PHPUnit\Framework\TestCase;

class RequestCollectorSingletonTest extends TestCase
{
    public function testGetTheSameInstance(): void
    {
        $instance1 = RequestCollectorSingleton::getInstance();
        $instance2 = RequestCollectorSingleton::getInstance();

        $this->assertSame($instance1, $instance2);
    }

    public function testSingletonFlow(): void
    {
        $instance1 = RequestCollectorSingleton::getInstance();
        $instance2 = RequestCollectorSingleton::getInstance();

        $instance1->enable();
        $instance2->store('foo', 'bar');

        $this->assertCount(1, $instance1->getAllStoredItems());
        $this->assertCount(1, $instance2->getAllStoredItems());
    }
}
