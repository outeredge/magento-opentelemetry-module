<?php

namespace OuterEdge\OpenTelemetry\Monolog\Handler;

use Magento\Framework\App\Config\ScopeConfigInterface;
use OuterEdge\OpenTelemetry\Logs\LazyLoggerProvider;
use OpenTelemetry\Contrib\Logs\Monolog\Handler;

class OpenTelemetry extends Handler
{
    const CONFIG_KEY_HEADERS  = 'oe_open_telemetry/settings/headers';
    const CONFIG_KEY_ENABLED  = 'oe_open_telemetry/settings/enable';
    const CONFIG_KEY_SERVICE  = 'oe_open_telemetry/settings/service';
    const CONFIG_KEY_ENDPOINT = 'oe_open_telemetry/settings/endpoint';
    const CONFIG_KEY_LOGLEVEL = 'oe_open_telemetry/settings/log_level';
    const CONFIG_KEY_LISTS    = 'oe_open_telemetry/settings/exclusion_lists';
    const CONFIG_KEY_PATTERNS = 'oe_open_telemetry/settings/exclusion_patterns';

    protected ?string $exclusionPattern = null;

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected LazyLoggerProvider $loggerProvider
    ) {
        if (!$this->isEnabled()) {
            return;
        }

        parent::__construct(
            $loggerProvider,
            $this->scopeConfig->getValue(self::CONFIG_KEY_LOGLEVEL),
            true
        );
    }

    protected function write($record): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        // Don't send this log entry to the collector if it matches one of the patterns
        if ($this->getPatternsAsRegex() && preg_match($this->getPatternsAsRegex(), $record['formatted']['message'])) {
            return;
        }

        parent::write($record);
    }

    protected function getPatternsAsRegex(): string|false
    {
        if (null === $this->exclusionPattern) {
            $patterns = [];
            foreach (explode(',', (string) $this->scopeConfig->getValue(self::CONFIG_KEY_LISTS)) as $listname) {
                $patternList = 'OuterEdge\OpenTelemetry\ExclusionList\\' . ucfirst($listname);
                if (class_exists($patternList)) {
                    $patterns = array_merge($patterns, $patternList::getPatterns());
                }
            }

            $customPatterns = trim((string) $this->scopeConfig->getValue(self::CONFIG_KEY_PATTERNS));
            $customPatterns = array_filter(array_map(function($rule) {
                return preg_quote(trim($rule), '/');
            }, explode("\n", $customPatterns)));

            $patterns = array_merge($patterns, $customPatterns);

            $pattern = implode('|', array_map(function($rule) {
                return preg_quote($rule, '/');
            }, $patterns));

            if (empty($pattern)) {
                $this->exclusionPattern = false;
            } else {
                $this->exclusionPattern = '/' . $pattern . '/';
            }
        }

        return $this->exclusionPattern;
    }

    protected function isEnabled()
    {
        return (bool) $this->scopeConfig->isSetFlag(self::CONFIG_KEY_ENABLED);
    }
}
