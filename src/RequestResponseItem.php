<?php

namespace Mmo\RequestCollector;

class RequestResponseItem
{
    private string $request;
    private string $response;
    private MetadataCollection $metadata;

    public function __construct(string $request, string $response, MetadataCollection $metadataCollection = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->metadata = $metadataCollection ?? new MetadataCollection();
    }

    public function getRequest(): string
    {
        return $this->request;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getMetadata(): MetadataCollection
    {
        return $this->metadata;
    }
}
