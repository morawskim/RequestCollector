<?php

namespace Mmo\RequestCollector;

use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class RequestCollectorSymfonyHttpClient implements HttpClientInterface
{
    use HttpClientTrait;

    private HttpClientInterface $client;
    private RequestCollector $requestCollector;

    public function __construct(HttpClientInterface $client, RequestCollector $requestCollector)
    {
        $this->client = $client;
        $this->requestCollector = $requestCollector;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        [, $options] = self::prepareRequest($method, $url, $options, static::OPTIONS_DEFAULTS);

        $options['body'] = self::getBodyAsString($options['body']);
        $response = $this->client->request($method, $url, $options);

        $this->requestCollector->store(
            $method . ' ' . $url . "\n" . implode("\n", $options['headers'])  . "\n\n". $options['body'],
            $this->convertResponseToString($response)
        );

        return $response;
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    private static function getBodyAsString($body): string
    {
        if (\is_resource($body)) {
            return stream_get_contents($body);
        }

        if (!$body instanceof \Closure) {
            return $body;
        }

        $result = '';

        while ('' !== $data = $body(self::$CHUNK_SIZE)) {
            if (!\is_string($data)) {
                throw new TransportException(sprintf('Return value of the "body" option callback must be string, "%s" returned.', get_debug_type($data)));
            }

            $result .= $data;
        }

        return $result;
    }

    private function convertResponseToString(ResponseInterface $response): string
    {
        $string = 'HTTP ' .  $response->getStatusCode() . "\n";
        foreach ($response->getHeaders(false) as $key => $value) {
            $string .= $key . ': ' . implode(";", $value) . "\n";
        }

        $string .= "\n";
        $string .= $response->getContent(false);

        return $string;
    }
}
