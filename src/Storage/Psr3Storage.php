<?php

namespace Mmo\RequestCollector\Storage;

use Mmo\RequestCollector\RequestCollector;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Psr3Storage implements StorageInterface
{
    private LoggerInterface $logger;
    private string $loglevel;

    public function __construct(LoggerInterface $logger, string $loglevel = LogLevel::DEBUG)
    {
        $this->logger = $logger;
        $this->loglevel = $loglevel;
    }

    public function store(RequestCollector $requestCollector): void
    {
        foreach ($requestCollector->getAllStoredItems() as $item) {
            $this->logger->log($this->loglevel, $item->getRequest());
            $this->logger->log($this->loglevel, $item->getResponse());
        }
    }
}
