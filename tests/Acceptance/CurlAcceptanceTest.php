<?php

namespace Mmo\RequestCollector;

use Mmo\RequestCollector\SanitizeData\ArrayKeyValuesSanitizeData;
use Mmo\RequestCollector\SanitizeData\JsonStringSanitizeData;
use PHPUnit\Framework\TestCase;

/**
 * @requires extension curl
 */
class CurlAcceptanceTest extends TestCase
{
    public function testCurlGet(): void
    {
        $url = 'https://jsonplaceholder.typicode.com/users';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        curl_close($curl);

        $requestCollector = new RequestCollector();
        $requestCollector->enable();
        $requestCollector->store("GET $url", $data);

        $this->assertCount(1, $requestCollector->getAllStoredItems());
        $this->assertSame("GET $url", $requestCollector->getAllStoredItems()[0]->getRequest());
        $this->assertSame(
            trim(file_get_contents(__DIR__ . '/_fixture/jsonplaceholder-users.json')),
            $requestCollector->getAllStoredItems()[0]->getResponse()
        );
    }

    public function testCurlPostWithSanitizeData(): void
    {
        $url = 'https://jsonplaceholder.typicode.com/comments';
        $fields = [
            "postId" => 1,
            "id" => 11,
            "name" => "id labore ex et quam laborum",
            "email" => "Eliseo@gardner.biz",
            "body" => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=UTF-8']);
        $data = curl_exec($curl);
        curl_close($curl);

        $requestCollector = new RequestCollector();
        $requestCollector->enable();
        $requestCollector->lazyStore(static function () use ($url, $fields, $data) {
            return new RequestResponseItem(
                sprintf(
                    "POST %s\n\n%s",
                    $url,
                    json_encode((new ArrayKeyValuesSanitizeData(['email']))->sanitizeData($fields))
                ),
                (new JsonStringSanitizeData(['email']))->sanitizeData($data)
            );
        });

        $this->assertCount(1, $requestCollector->getAllStoredItems());
        $this->assertStringEqualsFile(__DIR__ . '/_fixture/curl-post-request.txt', $requestCollector->getAllStoredItems()[0]->getRequest());
        $this->assertStringEqualsFile(__DIR__ . '/_fixture/curl-post-response.json', $requestCollector->getAllStoredItems()[0]->getResponse());
    }
}
