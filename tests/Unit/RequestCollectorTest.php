<?php

namespace Mmo\RequestCollector;

use PHPUnit\Framework\TestCase;

class RequestCollectorTest extends TestCase
{
    public function testEnableDisable(): void
    {
        $requestCollector = new RequestCollector();
        $this->assertCount(0, $requestCollector->getAllStoredItems());

        $requestCollector->store('foo', 'bar');
        $this->assertCount(0, $requestCollector->getAllStoredItems());

        $requestCollector->enable();
        $requestCollector->store('foo', 'bar');
        $this->assertCount(1, $requestCollector->getAllStoredItems());

        $requestCollector->disable();
        $requestCollector->store('foo', 'bar');
        $this->assertCount(1, $requestCollector->getAllStoredItems());
    }

    public function testStore(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();

        $requestCollector->store('foo', 'bar');
        $requestCollector->store('foo', 'bar');
        $this->assertCount(2, $requestCollector->getAllStoredItems());
    }

    public function testLazyStoreFlow(): void
    {
        $requestCollector = new RequestCollector();
        $this->assertCount(0, $requestCollector->getAllStoredItems());

        $requestCollector->lazyStore(fn () => 'wrong return type');
        $this->assertCount(0, $requestCollector->getAllStoredItems());

        $requestCollector->enable();
        $requestCollector->lazyStore(fn () => new RequestResponseItem('foo', 'bar'));
        $this->assertCount(1, $requestCollector->getAllStoredItems());

        $requestCollector->disable();
        $requestCollector->lazyStore(fn () => 'wrong return type');
        $this->assertCount(1, $requestCollector->getAllStoredItems());
    }

    public function testLazyStore(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();

        $requestCollector->lazyStore(fn () => new RequestResponseItem('foo', 'bar'));
        $requestCollector->lazyStore(fn () => new RequestResponseItem('foo', 'bar'));
        $this->assertCount(2, $requestCollector->getAllStoredItems());
    }

    public function testStoreWithMetadata(): void
    {
        $collection = new MetadataCollection();
        $collection->set('foo', MetadataValue::ofString('bar'));

        $requestCollector = new RequestCollector();
        $requestCollector->enable();

        $requestCollector->store('foo', 'bar', $collection);
        $this->assertCount(1, $requestCollector->getAllStoredItems());
        $this->assertSame('foo', $requestCollector->getAllStoredItems()[0]->getRequest());
        $this->assertSame('bar', $requestCollector->getAllStoredItems()[0]->getResponse());
        $this->assertEquals(['foo' => 'bar'], $requestCollector->getAllStoredItems()[0]->getMetadata()->toArray());
    }

    public function testLazyStoreWithMetadata(): void
    {
        $collection = new MetadataCollection();
        $collection->set('foo', MetadataValue::ofString('bar'));

        $requestCollector = new RequestCollector();
        $requestCollector->enable();

        $requestCollector->lazyStore(
            fn () => new RequestResponseItem('foo', 'bar', $collection)
        );
        $this->assertCount(1, $requestCollector->getAllStoredItems());
        $this->assertSame('foo', $requestCollector->getAllStoredItems()[0]->getRequest());
        $this->assertSame('bar', $requestCollector->getAllStoredItems()[0]->getResponse());
        $this->assertEquals(['foo' => 'bar'], $requestCollector->getAllStoredItems()[0]->getMetadata()->toArray());
    }
}
