<?php

namespace Mmo\RequestCollector;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<string, MetadataValue>
 */
class MetadataCollection implements IteratorAggregate
{
    /**
     * @var array<string, MetadataValue>
     */
    private array $data = [];

    public function set(string $key, MetadataValue $value): void
    {
        $this->data[$key] = $value;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this as $key => $value) {
            $data[$key] = $value->value();
        }

        return $data;
    }
}
