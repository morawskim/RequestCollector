<?php

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->addPsr4('Mmo\RequestCollector\\', __DIR__ . '/../tests/Unit/');
$loader->addPsr4('Mmo\RequestCollector\\', __DIR__ . '/../tests/Acceptance/');

class TestHelper {
    public static function getJsonPlaceholderUrl(): string
    {
        return getenv('JSON_PLACEHOLDER_URL');
    }

    public static function getJsonPlaceholderHostname(): string
    {
        $chunks = parse_url(self::getJsonPlaceholderUrl());

        if (isset($chunks['port'])) {
            return $chunks['host'] . ':' . $chunks['port'];
        }

        return $chunks['host'];
    }

    public static function buildJsonPlaceholderUrl(string $path): string
    {
        return self::getJsonPlaceholderUrl() . '/' . ltrim($path, '/');
    }

    public static function replaceUrlPlaceholderWithCurrentValue(string $payload): string
    {
        return str_replace('JSON_PLACEHOLDER_URL', rtrim(self::getJsonPlaceholderUrl(), '/'), $payload);
    }

    public static function replaceHostnamePlaceholderWithCurrentValue(string $payload): string
    {
        return str_replace('HOSTNAME_PLACEHOLDER', self::getJsonPlaceholderHostname(), $payload);
    }
}
