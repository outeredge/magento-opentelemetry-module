<?php

namespace OuterEdge\OpenTelemetry\Model\Api;

use OuterEdge\OpenTelemetry\Api\LoggerFrontendRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\StoreManagerInterface;

class LoggerFrontendRepository implements LoggerFrontendRepositoryInterface
{
    const CONFIG_KEY_ENABLE_FRONTEND = 'oe_open_telemetry/settings/enable_frontend';
    const CONFIG_KEY_ENABLE_CORS     = 'oe_open_telemetry/settings/enable_cors';

    protected ?bool $enabled = null;

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected LoggerInterface $logger,
        protected Request $request,
        protected StoreManagerInterface $storeManager
    ) {
        if (!$this->isEnabled()) {
            return json_encode(['success' => false, 'message' => 'Frontend Log is disabled']);
        }
    }

    /**
     * @inheritdoc
     */
    public function log($message)
    {
        if ($this->scopeConfig->isSetFlag(self::CONFIG_KEY_ENABLE_CORS)) {
            $url = parse_url($this->storeManager->getStore()->getBaseUrl());
            if ($this->request->getHeader('x-forwarded-host') != $url['host']) {
                return json_encode(['success' => false, 'message' => 'Blocked by CORS policy']);
            }
        }

        if (!isset($message)) {
            return json_encode(['success' => false, 'message' => 'Missing message']);
        }

        try {
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

        $this->enabled = (bool) $this->scopeConfig->isSetFlag(self::CONFIG_KEY_ENABLE_FRONTEND);

        return $this->enabled;
    }
}
