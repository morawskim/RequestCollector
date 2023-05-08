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
