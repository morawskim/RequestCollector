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
        $client = $this->getHttpClient($requestCollector);

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

    public function testSkipRequestCollectorOption(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();
        $client = $this->getHttpClient($requestCollector);

        $client->request('GET', 'https://jsonplaceholder.typicode.com/users', [GuzzleMiddleware::GUZZLE_OPTION_SKIP_REQUEST_COLLECTOR => true]);
        $client->request('GET', 'https://jsonplaceholder.typicode.com/users');

        $this->assertCount(1, $requestCollector->getAllStoredItems());
    }

    public function testPostRequest(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();
        $client = $this->getHttpClient($requestCollector);

        $client->request('POST', 'https://jsonplaceholder.typicode.com/comments', [
            'json' => [
                "postId" => 1,
                "id" => 11,
                "name" => "id labore ex et quam laborum",
                "email" => "Eliseo@gardner.biz",
                "body" => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"
            ]
        ]);

        $this->assertCount(1, $requestCollector->getAllStoredItems());
        $this->assertStringEqualsFile(
            __DIR__ . '/_fixture/guzzle-post-request.txt',
            str_replace("\r", '', $requestCollector->getAllStoredItems()[0]->getRequest())
        );
        $this->assertStringEqualsFile(
            __DIR__ . '/_fixture/guzzle-post-response.txt',
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
                str_replace("\r", '', $requestCollector->getAllStoredItems()[0]->getResponse())
            )
        );
    }

    private function getHttpClient(RequestCollector $requestCollector): Client
    {
        $stack = new HandlerStack();
        $stack->setHandler(choose_handler());
        $stack->push(GuzzleMiddleware::requestCollector($requestCollector));

        return new Client(['handler' => $stack, 'headers' => ['User-Agent' => 'RequestCollector/test']]);
    }
}
