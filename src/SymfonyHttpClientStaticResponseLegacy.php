<?php

namespace Mmo\RequestCollector;

use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SymfonyHttpClientStaticResponseLegacy implements ResponseInterface
{
    private int $statusCode;
    private array $headers;
    private string $content;

    public function __construct(int $statusCode, array $headers, string $content)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->content = $content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(bool $throw = true): array
    {
        return $this->headers;
    }

    public function getContent(bool $throw = true): string
    {
        return $this->content;
    }

    public function toArray(bool $throw = true): array
    {
        throw new class("Method not supported in this implementation") extends \BadMethodCallException implements DecodingExceptionInterface {
        };
    }

    public function cancel(): void
    {
    }

    public function getInfo(string $type = null)
    {
        return null;
    }
}
