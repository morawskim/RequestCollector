<?php

namespace Mmo\RequestCollector;

interface RuleInterface
{
    public function isSatisfiedBy(): bool;
}
