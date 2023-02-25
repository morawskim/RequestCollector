<?php

namespace Mmo\RequestCollector\SanitizeData;

class MaskString
{
    public static function scrambleValue(string $value): string
    {
        if (mb_strlen($value) < 4) {
            return '****';
        }

        $prefix = mb_substr($value, 0, 1);
        $stringToMask = mb_substr($value, 1, -1);
        $suffix = mb_substr($value, -1);

        return $prefix . str_repeat('*', mb_strlen($stringToMask)) . $suffix;
    }
}
