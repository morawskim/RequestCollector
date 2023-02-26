<?php

namespace Mmo\RequestCollector;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;

use function GuzzleHttp\choose_handler;

class GuzzleMiddlewareAcceptanceTest extends TestCase
{
    public function testMiddleware(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();

        $stack = new HandlerStack();
        $stack->setHandler(choose_handler());
        $stack->push(GuzzleMiddleware::requestCollector($requestCollector));
        $client = new Client(['handler' => $stack, 'headers' => ['User-Agent' => 'RequestCollector/test',]]);
        $response = $client->request('GET', 'https://jsonplaceholder.typicode.com/users');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonFile(__DIR__ . '/_fixture/jsonplaceholder-users.json', $response->getBody());
        $this->assertCount(1, $requestCollector->getAllStoredItems());

        $this->assertStringEqualsFile(
            __DIR__ . '/_fixture/request-collector-request.txt',
            $requestCollector->getAllStoredItems()[0]->getRequest()
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/_fixture/request-collector-response.txt',
            preg_replace(
                [
                    '/Date:.*$/m',
                    '/^Age:.*$/m',
                    '/^Server-Timing:.*$/m',
                    '/^Report-To:.*$/m',
                    '/^CF-RAY:.*$/m',
                    '/^X-Ratelimit-Limit:.*$/m',
                    '/^X-Ratelimit-Remaining:.*$/m',
                    '/^X-Ratelimit-Reset:.*$/m',
                ],
                '',
                $requestCollector->getAllStoredItems()[0]->getResponse()
            )
        );
    }
}
