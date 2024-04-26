<?php

namespace OuterEdge\OpenTelemetry\Monolog\Handler;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Monolog\Logger;
use Monolog\Handler\DeduplicationHandler;
use OuterEdge\OpenTelemetry\Monolog\Handler\OpenTelemetry;

class DeduplicationOpenTelemetry extends DeduplicationHandler
{
    const CONFIG_KEY_ENABLE_DEDUPLICATION = 'oe_open_telemetry/settings/enable_deduplication';

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected OpenTelemetry $handler
    ) {
        parent::__construct(
            $this->handler,
            null,
            $this->scopeConfig->getValue(OpenTelemetry::CONFIG_KEY_LOGLEVEL) ?? Logger::ERROR,
            $this->scopeConfig->getValue(self::CONFIG_KEY_ENABLE_DEDUPLICATION) ?? 60
        );
    }

    public function handle(array $record): bool
    {
        if (!$this->handler->isEnabled()) {
            return false;
        }

        return parent::write($record);
    }
}
