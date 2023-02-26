<?php

namespace Mmo\RequestCollector;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Promise\Create;

abstract class GuzzleMiddleware
{
    public const GUZZLE_OPTION_SKIP_REQUEST_COLLECTOR = 'skip_request_collector';

    public static function requestCollector(RequestCollector $requestCollector): \Closure
    {
        return static function (callable $handler) use ($requestCollector) {
            return static function ($request, array $options) use ($handler, $requestCollector) {
                $formatterForRequest = new MessageFormatter("{request}\n\nERROR:\n--------\n{error}");
                $formatterForResponse = new MessageFormatter("{response}\n\nERROR:\n--------\n{error}");

                if (true === ($options[self::GUZZLE_OPTION_SKIP_REQUEST_COLLECTOR] ?? null)) {
                    return $handler($request, $options);
                }

                return $handler($request, $options)->then(
                    function ($response) use ($request, $formatterForRequest, $formatterForResponse, $requestCollector) {
                        $requestCollector->store(
                            $formatterForRequest->format($request, $response),
                            $formatterForResponse->format($request, $response)
                        );

                        return $response;
                    },
                    function ($reason) use ($request, $formatterForRequest, $formatterForResponse, $requestCollector) {
                        $response = $reason instanceof RequestException
                            ? $reason->getResponse()
                            : null;

                        $requestCollector->store(
                            $formatterForRequest->format($request, $response),
                            $formatterForResponse->format($request, $response)
                        );

                        return Create::rejectionFor($reason);
                    }
                );
            };
        };
    }
}
