<?php

namespace OuterEdge\OpenTelemetry\Model\Api;

use OuterEdge\OpenTelemetry\Api\LoggerFrontendRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use \Psr\Log\LoggerInterface;

class LoggerFrontendRepository implements LoggerFrontendRepositoryInterface
{
    const CONFIG_KEY_ENABLED_FRONTEND   = 'oe_open_telemetry/settings/enable_frontend';

    protected ?bool $enabled = null;

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger
    ) {
        if (!$this->isEnabled()) {
            return json_encode(['success' => false, 'message' => 'Frontend Log is disabled']);
        }
    }

    /**
     * @inheritdoc
     */
    public function setLog($message)
    {
        if (!isset($message)) {
            return json_encode(['success' => false, 'message' => 'Missing message']);
         }

        try {
            $message = $this->serializer->serialize($message);
            $this->logger->error($message);


        } catch (\Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        return json_encode(['success' => true, 'message' => ["url" => 'Log saved']]);
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
