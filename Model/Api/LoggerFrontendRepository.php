<?php

namespace OuterEdge\OpenTelemetry\Model\Api;

use OuterEdge\OpenTelemetry\Api\LoggerFrontendRepositoryInterface;
use OuterEdge\OpenTelemetry\Monolog\Handler\OpenTelemetry;
use Magento\Framework\App\Config\ScopeConfigInterface;

class LoggerFrontendRepository implements LoggerFrontendRepositoryInterface
{
    const CONFIG_KEY_ENABLED_FRONTEND   = 'oe_open_telemetry/settings/enable_frontend';

    protected ?bool $enabled = null;

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected OpenTelemetry $openTelemetry
    ) {
        if (!$this->isEnabled()) {
            return json_encode(['success' => false, 'message' => 'Frontend Log is disabled']);
        }
    }

    /**
     * @inheritdoc
     */
    public function setLog($data)
    {
        if (!isset($data['answers']) || !isset($data['details'])) {
            return json_encode(['success' => false, 'message' => 'Missing data']);
        }

        try {
            //Send Data to Monolog


        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        return json_encode(['success' => true, 'message' => ["url" => 'XXX']]);
    }

    protected function isEnabled()
    {
        if (null !== $this->enabled) {
            return $this->enabled;
        }

        $this->enabled = (bool) $this->scopeConfig->isSetFlag(self::CONFIG_KEY_ENABLED_FRONTEND);

        return $this->enabled;
    }
}
