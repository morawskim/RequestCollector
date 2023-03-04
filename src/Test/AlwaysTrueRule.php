<?php

namespace Mmo\RequestCollector\Test;

use Mmo\RequestCollector\RuleInterface;

class AlwaysTrueRule implements RuleInterface
{
    public function isSatisfiedBy(): bool
    {
        return true;
    }
}
