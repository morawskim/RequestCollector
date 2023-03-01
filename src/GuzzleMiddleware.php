<?php

namespace Mmo\RequestCollector;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Promise\Create;
use Mmo\RequestCollector\SanitizeData\NoOpPsrMessageSanitizeData;
use Mmo\RequestCollector\SanitizeData\PsrMessageSanitizeDataInterface;

abstract class GuzzleMiddleware
{
    public const GUZZLE_OPTION_SKIP_REQUEST_COLLECTOR = 'skip_request_collector';
    public const GUZZLE_OPTION_SANITIZE_SERVICE = 'request_collector_sanitize_service';

    public static function requestCollector(RequestCollector $requestCollector): \Closure
    {
        return static function (callable $handler) use ($requestCollector) {
            return static function ($request, array $options) use ($handler, $requestCollector) {
                $formatterForRequest = new MessageFormatter("{request}\n\nERROR:\n--------\n{error}");
                $formatterForResponse = new MessageFormatter("{response}\n\nERROR:\n--------\n{error}");

                if (true === ($options[self::GUZZLE_OPTION_SKIP_REQUEST_COLLECTOR] ?? null)) {
                    return $handler($request, $options);
                }

                $sanitizeService = $options[self::GUZZLE_OPTION_SANITIZE_SERVICE] ?? new NoOpPsrMessageSanitizeData();

                if (!$sanitizeService instanceof PsrMessageSanitizeDataInterface) {
                    throw new \UnexpectedValueException(sprintf(
                        'The passed sanitize service must implement interface "%s". Got "%s" ',
                        PsrMessageSanitizeDataInterface::class,
                        is_object($sanitizeService) ? get_class($sanitizeService) : gettype($sanitizeService)
                    ));
                }

                return $handler($request, $options)->then(
                    function ($response) use ($request, $formatterForRequest, $formatterForResponse, $requestCollector, $sanitizeService) {
                        $sanitizedRequest = $sanitizeService->sanitizeRequestData($request);
                        $sanitizedResponse = $sanitizeService->sanitizeResponseData($response);

                        $requestCollector->store(
                            $formatterForRequest->format($sanitizedRequest, $sanitizedResponse),
                            $formatterForResponse->format($sanitizedRequest, $sanitizedResponse)
                        );

                        return $response;
                    },
                    function ($reason) use ($request, $formatterForRequest, $formatterForResponse, $requestCollector, $sanitizeService) {
                        $response = $reason instanceof RequestException
                            ? $reason->getResponse()
                            : null;

                        $sanitizedRequest = $sanitizeService->sanitizeRequestData($request);
                        $sanitizedResponse = $response ? $sanitizeService->sanitizeResponseData($response) : null;

                        $requestCollector->store(
                            $formatterForRequest->format($sanitizedRequest, $sanitizedResponse),
                            $formatterForResponse->format($sanitizedRequest, $sanitizedResponse)
                        );

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
