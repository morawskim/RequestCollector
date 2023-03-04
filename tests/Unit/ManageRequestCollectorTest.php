<?php

namespace Mmo\RequestCollector;

use Mmo\RequestCollector\Test\AlwaysTrueRule;
use Mmo\RequestCollector\Test\ThrowExceptionRule;
use PHPUnit\Framework\TestCase;

class ManageRequestCollectorTest extends TestCase
{
    public function testRequestCollectorShouldBeEnabledIfRuleIsSatisfied(): void
    {
        $requestCollector = new RequestCollector();
        $getIsEnabled = $this->createClosureToExtractIsEnabledFlag($requestCollector);

        $sut = new ManageRequestCollector([new AlwaysTrueRule()], $requestCollector);
        $sut->enableIfAtLeastOneRuleAllows();

        $this->assertTrue($getIsEnabled($requestCollector));
    }

    public function testBreakAfterFirstAllowedRule(): void
    {
        $requestCollector = new RequestCollector();
        $getIsEnabled = $this->createClosureToExtractIsEnabledFlag($requestCollector);

        $sut = new ManageRequestCollector([new AlwaysTrueRule(), new ThrowExceptionRule()], $requestCollector);
        $sut->enableIfAtLeastOneRuleAllows();
        $this->assertTrue($getIsEnabled($requestCollector));
    }

    private function createClosureToExtractIsEnabledFlag(RequestCollector $requestCollector): \Closure
    {
        return \Closure::bind(
            static fn (RequestCollector $requestCollector) => $requestCollector->isEnabled,
            null,
            $requestCollector
        );
    }
}
