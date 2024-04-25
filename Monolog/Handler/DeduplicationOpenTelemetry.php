<?php

namespace OuterEdge\OpenTelemetry\Monolog\Handler;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Monolog\Logger;
use OuterEdge\OpenTelemetry\Logs\LazyLoggerProvider;
use Monolog\Handler\DeduplicationHandler;
use OuterEdge\OpenTelemetry\Monolog\Handler\OpenTelemetry;

class DeduplicationOpenTelemetry extends DeduplicationHandler
{
    const CONFIG_KEY_ENABLED_DEDUPLICATION   = 'oe_open_telemetry/settings/enabled_deduplication';

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected State $appState,
        protected LazyLoggerProvider $loggerProvider
    ) {

        parent::__construct(
            new OpenTelemetry($this->scopeConfig, $this->loggerProvider, $this->appState),
            null,
            Logger::toMonologLevel($this->scopeConfig->getValue(OpenTelemetry::CONFIG_KEY_LOGLEVEL) ?? Logger::ERROR),
            $this->scopeConfig->getValue(self::CONFIG_KEY_ENABLED_DEDUPLICATION) ?? 0,
            true
        );
    }
}
