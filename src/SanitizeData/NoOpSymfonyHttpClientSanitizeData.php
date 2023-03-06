<?php

namespace Mmo\RequestCollector\SanitizeData;

use Mmo\RequestCollector\SymfonyHttpClientStaticResponse;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class NoOpSymfonyHttpClientSanitizeData implements SymfonyHttpClientSanitizeDataInterface
{
    public function sanitizeRequest(string $body): string
    {
        return $body;
    }

    public function sanitizeResponse(ResponseInterface $response): ResponseInterface
    {
        return new SymfonyHttpClientStaticResponse(
            $response->getStatusCode(),
            $response->getHeaders(false),
            $response->getContent(false)
        );
    }
}
