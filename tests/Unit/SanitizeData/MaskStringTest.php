<?php

namespace Mmo\RequestCollector\SanitizeData;

use PHPUnit\Framework\TestCase;

class MaskStringTest extends TestCase
{
    /**
     * @dataProvider providerStringValues
     */
    public function testMaskValue(string $value, string $expected): void
    {
        $this->assertSame($expected, MaskString::scrambleValue($value));
    }

    public function providerStringValues(): iterable
    {
        yield 'empty' => ['', '****'];

        yield 'one_char' => ['a', '****'];

        yield 'thee_utf8_chars' => ['ąąą', '****'];

        yield 'five_chars' => ['aaaaa', 'a***a'];

        yield 'utf8' => ['ąąąąą', 'ą***ą'];

        yield 'long_string' => ['aabbccddeeff', 'a**********f'];
    }
}
