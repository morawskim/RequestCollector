<?php

namespace Mmo\RequestCollector\Storage;

use Mmo\RequestCollector\RequestCollector;

interface StorageInterface
{
    public function store(RequestCollector $requestCollector): void;
}
