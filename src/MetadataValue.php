<?php

namespace Mmo\RequestCollector;

class MetadataValue
{
    /**
     * @var string|int|float
     */
    private string $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function ofString(string $value): self
    {
        return new MetadataValue($value);
    }

    public static function ofInt(int $value): self
    {
        return new MetadataValue($value);
    }

    public static function ofFloat(float $value): self
    {
        return new MetadataValue($value);
    }

    /**
     * @return string|int|float
     */
    public function value()
    {
        return $this->value;
    }
}
