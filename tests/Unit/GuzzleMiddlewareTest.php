<?php

namespace Mmo\RequestCollector;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GuzzleMiddlewareTest extends TestCase
{
    public function testRequestCollectorMiddlewareWhenCollectingIsEnabled(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();

        $expectedRequest = "PUT / HTTP/1.1\r\nHost: www.google.com\r\nX-Foo: Bar\r\n\r\nLorem ipsum\n\nERROR:\n--------\nNULL";
        $expectedResponse = "HTTP/1.1 404 Not Found\r\n\r\n\n\nERROR:\n--------\nNULL";

        $handler = new MockHandler([new Response(404)]);
        $stack = new HandlerStack($handler);
        $stack->push(GuzzleMiddleware::requestCollector($requestCollector));
        $comp = $stack->resolve();
        $promise = $comp(new Request('PUT', 'https://www.google.com', ['X-Foo' => 'Bar'], 'Lorem ipsum'), []);
        $promise->wait(false);

        $storedItems = $requestCollector->getAllStoredItems();
        $this->assertCount(1, $storedItems);
        $this->assertEquals($expectedRequest, $storedItems[0]->getRequest());
        $this->assertEquals($expectedResponse, $storedItems[0]->getResponse());
    }

    public function testRequestCollectorMiddlewareWhenCollectingIsDisabled(): void
    {
        $requestCollector = new RequestCollector();

        $handler = new MockHandler([new Response(404)]);
        $stack = new HandlerStack($handler);
        $stack->push(GuzzleMiddleware::requestCollector($requestCollector));
        $comp = $stack->resolve();
        $promise = $comp(new Request('PUT', 'https://www.google.com'), []);
        $promise->wait(false);

        $storedItems = $requestCollector->getAllStoredItems();
        $this->assertCount(0, $storedItems);
    }

    public function testOptionToDisableRequestCollector(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->enable();
        $requestCollector->store('foo', 'bar');

        $handler = new MockHandler([new Response(404)]);
        $stack = new HandlerStack($handler);
        $stack->push(GuzzleMiddleware::requestCollector($requestCollector));
        $comp = $stack->resolve();
        $promise = $comp(
            new Request('PUT', 'https://www.google.com'),
            [GuzzleMiddleware::GUZZLE_OPTION_SKIP_REQUEST_COLLECTOR => true]
        );
        $promise->wait(false);

        $storedItems = $requestCollector->getAllStoredItems();
        $this->assertCount(1, $storedItems);
    }
}
