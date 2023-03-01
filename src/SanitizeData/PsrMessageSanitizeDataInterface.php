<?php

namespace Mmo\RequestCollector\SanitizeData;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface PsrMessageSanitizeDataInterface
{
    public const DEFAULT_SENSITIVE_HEADERS = [
        'Authorization',
        'Cookie',
        'Set-Cookie',
        'X-Forwarded-For',
        'X-Real-IP',
    ];

    public function sanitizeRequestData(RequestInterface $request): RequestInterface;

    public function sanitizeResponseData(ResponseInterface $response): ResponseInterface;
}
