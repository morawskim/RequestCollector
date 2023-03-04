<?php

namespace Mmo\RequestCollector\Storage;

use Mmo\RequestCollector\MetadataCollection;
use Mmo\RequestCollector\MetadataValue;
use Mmo\RequestCollector\RequestCollector;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Psr3StorageTest extends TestCase
{
    public function testStoreRequestCollectorData(): void
    {
        $logger = new class extends AbstractLogger {
            private array $logsByLevel = [];

            public function log($level, $message, array $context = []): void
            {
                $this->logsByLevel[$level] ??= [];
                $this->logsByLevel[$level][] = $message;
            }

            public function getLogsByLevel(string $level): array
            {
                return $this->logsByLevel[$level] ?? [];
            }
        };

        $requestCollector = new RequestCollector();
        $requestCollector->enable();
        $requestCollector->store('foo', 'bar');

        $sut = new Psr3Storage($logger, LogLevel::NOTICE);
        $sut->store($requestCollector);

        $this->assertCount(2, $logger->getLogsByLevel(LogLevel::NOTICE));
        $this->assertSame('foo', $logger->getLogsByLevel(LogLevel::NOTICE)[0]);
        $this->assertSame('bar', $logger->getLogsByLevel(LogLevel::NOTICE)[1]);
    }

    public function testStoreMetadata(): void
    {
        $logger = new class extends AbstractLogger {
            private array $logsByLevel = [];

            public function log($level, $message, array $context = []): void
            {
                $this->logsByLevel[$level] ??= [];
                $this->logsByLevel[$level][] = ['message' => $message, 'context' => $context];
            }

            public function getLogsByLevel(string $level): array
            {
                return $this->logsByLevel[$level] ?? [];
            }
        };

        $collection = new MetadataCollection();
        $collection->set('baz', MetadataValue::ofString("lorem ipsum"));

        $requestCollector = new RequestCollector();
        $requestCollector->enable();
        $requestCollector->store('foo', 'bar', $collection);

        $sut = new Psr3Storage($logger, LogLevel::NOTICE);
        $sut->store($requestCollector);

        $this->assertCount(2, $logger->getLogsByLevel(LogLevel::NOTICE));
        $this->assertSame(['message' => 'foo', 'context' => ['baz' => 'lorem ipsum']], $logger->getLogsByLevel(LogLevel::NOTICE)[0]);
        $this->assertSame(['message' => 'bar', 'context' => ['baz' => 'lorem ipsum']], $logger->getLogsByLevel(LogLevel::NOTICE)[1]);
    }
}
