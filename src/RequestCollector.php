<?php

namespace Mmo\RequestCollector;

class RequestCollector
{
    private \ArrayObject $requestStorage;

    private bool $isEnabled = false;

    public function __construct()
    {
        $this->requestStorage = new \ArrayObject([]);
    }

    public function enable(): void
    {
        $this->isEnabled = true;
    }

    public function disable(): void
    {
        $this->isEnabled = false;
    }

    public function store(string $request, string $response, MetadataCollection $metadata = null): void
    {
        if ($this->isEnabled) {
            $this->requestStorage->append(new RequestResponseItem($request, $response, $metadata ?? new MetadataCollection()));
        }
    }

    /**
     * @param callable(): RequestResponseItem $callable
     * @return void
     */
    public function lazyStore(callable $callable): void
    {
        if ($this->isEnabled) {
            $result = $callable();

            if (!$result instanceof RequestResponseItem) {
                $type = is_object($result) ? get_class($result) : gettype($result);
                throw new \BadMethodCallException(sprintf(
                    'The passed callable MUST return "%s", but got "%s"',
                    RequestResponseItem::class,
                    $type
                ));
            }

            $this->requestStorage->append($result);
        }
    }

    /**
     * @return RequestResponseItem[]
     */
    public function getAllStoredItems(): iterable
    {
        return $this->requestStorage->getIterator();
    }
}
