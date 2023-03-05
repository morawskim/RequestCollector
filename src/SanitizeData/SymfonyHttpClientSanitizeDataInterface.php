<?php

namespace Mmo\RequestCollector\SanitizeData;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface SymfonyHttpClientSanitizeDataInterface
{
    public function sanitizeResponse(ResponseInterface $response): ResponseInterface;
}
