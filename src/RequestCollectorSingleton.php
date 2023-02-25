<?php

namespace Mmo\RequestCollector;

class RequestCollectorSingleton
{
    private static ?RequestCollector $instance;

    private function __construct()
    {
    }

    protected function __clone()
    {
        throw new \BadMethodCallException(' Singletons should not be cloneable.');
    }

    public function __wakeup()
    {
        throw new \BadMethodCallException("Cannot unserialize a singleton.");
    }

    public static function getInstance(): RequestCollector
    {
        if (!isset(self::$instance)) {
            self::$instance = new RequestCollector();
        }

        return self::$instance;
    }
}
