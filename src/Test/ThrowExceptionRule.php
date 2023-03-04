<?php

namespace Mmo\RequestCollector\Test;

use Mmo\RequestCollector\RuleInterface;

class ThrowExceptionRule implements RuleInterface
{
    public function isSatisfiedBy(): bool
    {
        throw new \RuntimeException('this exception should never been thrown');
    }
}
