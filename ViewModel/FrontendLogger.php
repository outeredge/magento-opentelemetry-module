<?php

namespace OuterEdge\OpenTelemetry\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use OuterEdge\OpenTelemetry\Model\Api\LoggerFrontendRepository;

class FrontendLogger implements ArgumentInterface
{
    public function __construct(
        protected ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(): string
    {
        return $this->scopeConfig->getValue(LoggerFrontendRepository::CONFIG_KEY_ENABLE_FRONTEND);
    }
}
