<?php

namespace Mmo\RequestCollector;

class ManageRequestCollector
{
    /**
     * @var RuleInterface[]
     */
    private array $rules;
    private RequestCollector $requestCollector;

    public function __construct(array $rules, RequestCollector $requestCollector)
    {
        $this->rules = $rules;
        $this->requestCollector = $requestCollector;
    }

    public function enableIfAtLeastOneRuleAllows(): void
    {
        foreach ($this->rules as $rule) {
            if ($rule->isSatisfiedBy()) {
                $this->requestCollector->enable();
                break;
            }
        }
    }
}
