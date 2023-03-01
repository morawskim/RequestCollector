<?php

namespace Mmo\RequestCollector\SanitizeData;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class NoOpPsrMessageSanitizeData implements PsrMessageSanitizeDataInterface
{
    public function sanitizeRequestData(RequestInterface $request): RequestInterface
    {
        return $request;
    }

    public function sanitizeResponseData(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }
}
