<?php

namespace OuterEdge\OpenTelemetry\Monolog\Handler;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Monolog\Logger;
use OuterEdge\OpenTelemetry\Logs\LazyLoggerProvider;
use Monolog\Handler\DeduplicationHandler;
use OuterEdge\OpenTelemetry\Monolog\Handler\OpenTelemetry;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\UrlInterface;

class DeduplicationOpenTelemetry extends DeduplicationHandler
{
    const CONFIG_KEY_ENABLED_DEDUPLICATION   = 'oe_open_telemetry/settings/enabled_deduplication';

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected State $appState,
        protected ProductMetadataInterface $productMetadata,
        protected UrlInterface $urlInterface
    ) {

        $loggerProvider = new LazyLoggerProvider($this->scopeConfig, $this->appState, $this->productMetadata, $this->urlInterface);
        $openTelemetry = new OpenTelemetry($this->scopeConfig, $loggerProvider, $this->appState);

        parent::__construct(
            $openTelemetry,
            null,
            Logger::ERROR,
            $this->scopeConfig->getValue(self::CONFIG_KEY_ENABLED_DEDUPLICATION),
            true);

    }
}
