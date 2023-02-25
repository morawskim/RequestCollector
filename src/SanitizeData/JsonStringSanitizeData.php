<?php

namespace Mmo\RequestCollector\SanitizeData;

class JsonStringSanitizeData
{
    /**
     * @var string[]
     */
    private array $jsonFieldsToSanitize;

    public function __construct(array $jsonFieldsToSanitize)
    {
        $this->jsonFieldsToSanitize = $jsonFieldsToSanitize;
    }

    public function sanitizeData(string $jsonString): string
    {
        return preg_replace_callback(
            array_map(
                static fn (string $key) => sprintf('/"(%s)"\s*:\s*"(.*?)"/',
                preg_quote($key, '/')), $this->jsonFieldsToSanitize
            ),
            static fn ($matches) => sprintf('"%s":"%s"', $matches[1], MaskString::scrambleValue($matches[2])),
            $jsonString
        );
    }
}
