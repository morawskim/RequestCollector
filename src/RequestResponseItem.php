<?php

namespace Mmo\RequestCollector;

class RequestResponseItem
{
    private string $request;
    private string $response;

    public function __construct(string $request, string $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest(): string
    {
        return $this->request;
    }

    public function getResponse(): string
    {
        return $this->response;
    }
}
