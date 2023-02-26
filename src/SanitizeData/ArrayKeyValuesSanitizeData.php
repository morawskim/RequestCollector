<?php

namespace Mmo\RequestCollector\SanitizeData;

class ArrayKeyValuesSanitizeData
{
    private array $arrayKeysToSanitize;

    public function __construct(array $arrayKeysToSanitize)
    {
        $this->arrayKeysToSanitize = $arrayKeysToSanitize;
    }

    public function sanitizeData(array $data): array
    {
        array_walk_recursive($data, function (&$value, $key) {
            if (!in_array($key, $this->arrayKeysToSanitize, true)) {
                return;
            }

            if (is_string($value)) {
                $value = MaskString::scrambleValue($value);
            }
        });

        return $data;
    }
}
