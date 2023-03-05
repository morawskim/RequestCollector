<?php

namespace Mmo\RequestCollector;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class RequestCollectorSymfonyHttpClientTest extends TestCase
{
    // todo check 5xx/4xx status code
    // todo sanitize request/response

    public function testSkipRequestCollectorOption(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();

        $client = new RequestCollectorSymfonyHttpClient(
            HttpClient::create(),
            $requestCollector
        );

        $client->request('GET', 'https://jsonplaceholder.typicode.com/users', ['extra' => [RequestCollectorSymfonyHttpClient::OPTION_SKIP_REQUEST_COLLECTOR => true]]);
        $client->request('GET', 'https://jsonplaceholder.typicode.com/users');

        $this->assertCount(1, $requestCollector->getAllStoredItems());
    }

    public function testGetRequest(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();

        $sut = new RequestCollectorSymfonyHttpClient(
            HttpClient::create(),
            $requestCollector
        );

        $sut->request('GET', 'https://jsonplaceholder.typicode.com/users');


        $this->assertCount(1, $requestCollector->getAllStoredItems());
        $this->assertStringEqualsFile(
            __DIR__ . '/_fixture/symfony-http-client-get-request.txt',
            $requestCollector->getAllStoredItems()[0]->getRequest()
        );
        $this->assertStringEqualsFile(
            __DIR__ . '/_fixture/symfony-http-client-get-response.txt',
            preg_replace(
                [
                    '/^date:.*\n/m',
                    '/^age:.*\n/m',
                    '/^server-timing:.*\n/m',
                    '/^report-to:.*\n/m',
                    '/^cf-ray:.*\n/m',
                    '/^x-ratelimit-limit:.*\n/m',
                    '/^x-ratelimit-remaining:.*\n/m',
                    '/^x-ratelimit-reset:.*\n/m',
                ],
                '',
                $requestCollector->getAllStoredItems()[0]->getResponse()
            )
        );
    }

    public function testPost(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();

        $sut = new RequestCollectorSymfonyHttpClient(
            HttpClient::create(),
            $requestCollector
        );

        $sut->request('POST', 'https://jsonplaceholder.typicode.com/comments', [
            'json' => [
                "postId" => 1,
                "id" => 11,
                "name" => "id labore ex et quam laborum",
                "email" => "Eliseo@gardner.biz",
                "body" => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"
            ],
        ]);

        $this->assertCount(1, $requestCollector->getAllStoredItems());
        $this->assertStringEqualsFile(
            __DIR__ . '/_fixture/symfony-http-client-post-request.txt',
            $requestCollector->getAllStoredItems()[0]->getRequest()
        );
        $this->assertStringEqualsFile(
            __DIR__ . '/_fixture/symfony-http-client-post-response.txt',
            preg_replace(
                [
                    '/^date:.*\n/m',
                    '/^age:.*\n/m',
                    '/^server-timing:.*\n/m',
                    '/^report-to:.*\n/m',
                    '/^cf-ray:.*\n/m',
                    '/^x-ratelimit-limit:.*\n/m',
                    '/^x-ratelimit-remaining:.*\n/m',
                    '/^x-ratelimit-reset:.*\n/m',
                ],
                '',
                $requestCollector->getAllStoredItems()[0]->getResponse()
            )
        );
    }
}
