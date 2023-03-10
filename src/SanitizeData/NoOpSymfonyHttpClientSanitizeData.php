<?php

namespace Mmo\RequestCollector\SanitizeData;

use Mmo\RequestCollector\SymfonyHttpClientStaticResponse;
use Mmo\RequestCollector\SymfonyHttpClientStaticResponseLegacy;
use Symfony\Contracts\HttpClient\ResponseInterface;

class NoOpSymfonyHttpClientSanitizeData implements SymfonyHttpClientSanitizeDataInterface
{
    public function sanitizeRequest(string $body): string
    {
        return $body;
    }

    public function sanitizeResponse(ResponseInterface $response): ResponseInterface
    {
        if (\PHP_VERSION_ID < 80000) {
            return new SymfonyHttpClientStaticResponseLegacy(
                $response->getStatusCode(),
                $response->getHeaders(false),
                $response->getContent(false)
            );
        }

        return new SymfonyHttpClientStaticResponse(
            $response->getStatusCode(),
            $response->getHeaders(false),
            $response->getContent(false)
        );
    }
}
