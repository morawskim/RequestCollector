<?php

namespace Mmo\RequestCollector\SanitizeData;

use PHPUnit\Framework\TestCase;

class ArrayKeyValuesSanitizeDataTest extends TestCase
{
    public function testSanitizeArray(): void
    {
        $sut = new ArrayKeyValuesSanitizeData(['token']);

        $result = $sut->sanitizeData(['foo' => ['bar' => ['token' => 'secret']]]);
        $this->assertSame(['foo' => ['bar' => ['token' => 's****t']]], $result);
    }

    /**
     * @dataProvider providerArray
     */
    public function testSanitizeJsonStringArray(array $data, array $expectedArray): void
    {
        $sut = new ArrayKeyValuesSanitizeData(['token', 'first.name']);

        $result = $sut->sanitizeData($data);
        $this->assertEquals($expectedArray, $result);
    }

    public function providerArray(): iterable
    {
        yield 'empty' => [
            [],
            []
        ];

        yield 'one_field' => [
            ['token' => 'secret'],
            ['token' => 's****t'],
        ];

        yield 'field_not_exists' => [
            ['foo' => 'bar'],
            ['foo' => 'bar'],
        ];

        yield 'nested_field' => [
            ['foo' => ['token' => 'secret']],
            ['foo' => ['token' => 's****t']],
        ];

        yield 'dot_in_field_name' => [
            ['foo' => ['first.name' => 'secret', 'first2name' => 'foo']],
            ['foo' => ['first.name' => 's****t', 'first2name' => 'foo']],
        ];

        yield 'only_string_value' => [
            ['foo' => ['token' => 123]],
            ['foo' => ['token' => 123]],
        ];
    }
}
