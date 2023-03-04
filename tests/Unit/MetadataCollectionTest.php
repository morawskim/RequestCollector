<?php

namespace Mmo\RequestCollector;

use PHPUnit\Framework\TestCase;

class MetadataCollectionTest extends TestCase
{
    public function testSet(): void
    {
        $sut = new MetadataCollection();
        $sut->set('foo', MetadataValue::ofString('bar'));

        $this->assertEquals(['foo' => 'bar'], $sut->toArray());
    }

    public function testSetOverwriteOldValue(): void
    {
        $sut = new MetadataCollection();
        $sut->set('foo', MetadataValue::ofString('bar'));
        $sut->set('foo', MetadataValue::ofString('baz'));

        $this->assertEquals(['foo' => 'baz'], $sut->toArray());
    }

    public function testDifferentTypes(): void
    {
        $sut = new MetadataCollection();
        $sut->set('string', MetadataValue::ofString('bar'));
        $sut->set('int', MetadataValue::ofInt(4));
        $sut->set('float', MetadataValue::ofFloat(100.10));

        $this->assertEquals(['string' => 'bar', 'int' => 4, 'float' => 100.10], $sut->toArray());
    }
}
