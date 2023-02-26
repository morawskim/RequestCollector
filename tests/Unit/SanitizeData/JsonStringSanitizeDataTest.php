<?php

namespace Mmo\RequestCollector\SanitizeData;

use PHPUnit\Framework\TestCase;

class JsonStringSanitizeDataTest extends TestCase
{
    public function testSanitizeJsonString(): void
    {
        $sut = new JsonStringSanitizeData(['token']);

        $result = $sut->sanitizeData(json_encode(['foo' => ['bar' => ['token' => 'secret']]]));
        $this->assertSame('{"foo":{"bar":{"token":"s****t"}}}', $result);
    }


    /**
     * @dataProvider providerJsonStrings
     */
    public function testSanitizeJsonStringArray(string $jsonString, string $expectedJsonString): void
    {
        $sut = new JsonStringSanitizeData(['token', 'first.name', 'first\/name']);

        $result = $sut->sanitizeData($jsonString);
        $this->assertSame($expectedJsonString, $result);
    }

    public function providerJsonStrings(): iterable
    {
        yield 'empty' => [
            '',
            ''
        ];

        yield 'one_field' => [
            json_encode(['token' => 'secret']),
            '{"token":"s****t"}'
        ];

        yield 'field_not_exists' => [
            json_encode(['foo' => 'bar']),
            '{"foo":"bar"}'
        ];

        yield 'nested_field' => [
            json_encode(['foo' => ['token' => 'secret']]),
            '{"foo":{"token":"s****t"}}'
        ];

        $result = <<<EOS
        {
            "foo": {
                "token":"s****t"
            }
        }
        EOS;
        yield 'nested_field_pretty_print' => [
            json_encode(['foo' => ['token' => 'secret']], JSON_PRETTY_PRINT),
            $result
        ];

        $result = <<<EOS
        {
            "foo": {
                "first.name":"s****t",
                "first2name": "foo"
            }
        }
        EOS;
        yield 'dot_in_field_name' => [
            json_encode(['foo' => ['first.name' => 'secret', 'first2name' => 'foo']], JSON_PRETTY_PRINT),
            $result
        ];

        yield 'slash' => [
            json_encode(['foo' => ['first/name' => 'secret', 'foo' => 'foo']]),
            '{"foo":{"first\/name":"s****t","foo":"foo"}}'
        ];

        yield 'only_string_value' => [
            '{"token":123}',
            '{"token":123}',
        ];

        yield 'invalid_json' => [
            '{"token}',
            '{"token}',
        ];
    }
}
