# RequestCollector

Library to collect the request/response sent via Symfony HttpClient or Guzzle.

## Why and main goal

In one of commercial projects we had more than 70 integrations with external services.
For many of them we didn't have neither test accounts nor test environments.
Even when we had credentials still there were problems to test edge-case issues or prepare fake data.
The goal of this library is to help debugging those integrations even on production environments.
Each sent request and response can be logged and sanitized from private/personal data.

## Usage

### Guzzle 6+

```php
<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Mmo\RequestCollector\Guzzle\GuzzleMiddleware;
use Mmo\RequestCollector\RequestCollector;

use function GuzzleHttp\choose_handler;

require_once __DIR__ . '/vendor/autoload.php';

$requestCollector = new RequestCollector();
$requestCollector->enable();

$stack = new HandlerStack();
$stack->setHandler(choose_handler());
$stack->push(GuzzleMiddleware::requestCollector($requestCollector));

$client =  new Client(['handler' => $stack, 'headers' => ['User-Agent' => 'RequestCollector/test']]);
$response = $client->request('GET', 'https://jsonplaceholder.typicode.com/users');

var_dump($requestCollector->getAllStoredItems());

```

### Symfony/HttpClient 4+

```php
<?php

use Mmo\RequestCollector\RequestCollector;
use Mmo\RequestCollector\SymfonyHttpClient\RequestCollectorSymfonyHttpClient;
use Symfony\Component\HttpClient\HttpClient;

require_once __DIR__ . '/vendor/autoload.php';

$requestCollector = new RequestCollector();
$requestCollector->enable();

$sut = new RequestCollectorSymfonyHttpClient(
    HttpClient::create(),
    $requestCollector
);

$sut->request('GET', 'https://jsonplaceholder.typicode.com/users');

var_dump($requestCollector->getAllStoredItems());

```

## Sensitive data

### Guzzle

```php
<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Mmo\RequestCollector\Guzzle\GuzzleMiddleware;
use Mmo\RequestCollector\RequestCollector;
use Mmo\RequestCollector\SanitizeData\JsonStringSanitizeData;
use Mmo\RequestCollector\SanitizeData\PsrMessageSanitizeDataInterface;
use Mmo\RequestCollector\Test\GuzzleUtils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function GuzzleHttp\choose_handler;

require_once __DIR__ . '/vendor/autoload.php';

$requestCollector = new RequestCollector();
$requestCollector->enable();

$stack = new HandlerStack();
$stack->setHandler(choose_handler());
$stack->push(GuzzleMiddleware::requestCollector($requestCollector));

$client = new Client(['handler' => $stack, 'headers' => ['User-Agent' => 'RequestCollector/test']]);

$response = $client->request('POST', 'https://jsonplaceholder.typicode.com/comments', [
    'json' => [
        "postId" => 1,
        "id" => 11,
        "name" => "id labore ex et quam laborum",
        "email" => "Eliseo@gardner.biz",
        "body" => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"
    ],
    GuzzleMiddleware::GUZZLE_OPTION_SANITIZE_SERVICE => new class(new JsonStringSanitizeData(['email'])) implements PsrMessageSanitizeDataInterface {
        private JsonStringSanitizeData $jsonStringSanitizeData;

        public function __construct(JsonStringSanitizeData $jsonStringSanitizeData)
        {
            $this->jsonStringSanitizeData = $jsonStringSanitizeData;
        }

        public function sanitizeRequestData(RequestInterface $request): RequestInterface
        {
            return $request->withBody(
                GuzzleUtils::streamFor(
                    $this->jsonStringSanitizeData->sanitizeData((string)$request->getBody())
                )
            );
        }

        public function sanitizeResponseData(ResponseInterface $response): ResponseInterface
        {
            return $response->withBody(
                GuzzleUtils::streamFor(
                    $this->jsonStringSanitizeData->sanitizeData((string)$response->getBody())
                )
            );
        }
    },
]);

var_dump($requestCollector->getAllStoredItems());

```

### Symfony/HttpClient

```php
<?php

use Mmo\RequestCollector\RequestCollector;
use Mmo\RequestCollector\SanitizeData\JsonStringSanitizeData;
use Mmo\RequestCollector\SanitizeData\SymfonyHttpClientSanitizeDataInterface;
use Mmo\RequestCollector\SymfonyHttpClient\RequestCollectorSymfonyHttpClient;
use Mmo\RequestCollector\SymfonyHttpClient\SymfonyHttpClientStaticResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

require_once __DIR__ . '/vendor/autoload.php';

$requestCollector = new RequestCollector();
$requestCollector->enable();

$client = new RequestCollectorSymfonyHttpClient(
    HttpClient::create(),
    $requestCollector
);

$client->request('POST', 'https://jsonplaceholder.typicode.com/comments', [
    'json' => [
        "postId" => 1,
        "id" => 11,
        "name" => "id labore ex et quam laborum",
        "email" => "Eliseo@gardner.biz",
        "body" => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"
    ],
    'extra' => [
        RequestCollectorSymfonyHttpClient::OPTION_SANITIZE_SERVICE => new class(new JsonStringSanitizeData(['email', 'message'])) implements SymfonyHttpClientSanitizeDataInterface {
            private JsonStringSanitizeData $jsonStringSanitizeData;

            public function __construct(JsonStringSanitizeData $jsonStringSanitizeData)
            {
                $this->jsonStringSanitizeData = $jsonStringSanitizeData;
            }

            public function sanitizeRequest(string $body): string
            {
                return $this->jsonStringSanitizeData->sanitizeData($body);
            }

            public function sanitizeResponse(ResponseInterface $response): ResponseInterface
            {
                return new SymfonyHttpClientStaticResponse(
                    $response->getStatusCode(),
                    $response->getHeaders(false),
                    $this->jsonStringSanitizeData->sanitizeData($response->getContent(false))
                );
            }
        }
    ]
]);

var_dump($requestCollector->getAllStoredItems());

```
