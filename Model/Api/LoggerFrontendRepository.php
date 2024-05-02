<?php

namespace OuterEdge\OpenTelemetry\Model\Api;

use OuterEdge\OpenTelemetry\Api\LoggerFrontendRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\StoreManagerInterface;
use OuterEdge\OpenTelemetry\Monolog\Handler\OpenTelemetry;

class LoggerFrontendRepository implements LoggerFrontendRepositoryInterface
{
    const CONFIG_KEY_ENABLE_FRONTEND = 'oe_open_telemetry/settings/enable_frontend';

    protected ?bool $enabled = null;

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected LoggerInterface $logger,
        protected Request $request,
        protected StoreManagerInterface $storeManager
    ) {
        if (!$this->isEnabled()) {
            return [['success' => false, 'message' => 'Frontend Log is disabled']];
        }
    }

    /**
     * @inheritdoc
     */
    public function log($errors)
    {
        $parseUrl = parse_url($this->storeManager->getStore()->getBaseUrl());
        $domain = $parseUrl['host'];
        $url = $parseUrl['scheme']."://".$parseUrl['host'];

        if (!$this->request->isXmlHttpRequest() ||
            $this->request->getHeader('x-forwarded-host') != $domain ||
            $this->request->getHeader('origin') != $url ||
            $this->request->getHeader('sec-fetch-site') != 'same-origin') {
            return [['success' => false, 'message' => 'Blocked by CORS policy']];
        }

        foreach ($errors as $error) {

            if (!isset($error['message'])) {
                return [['success' => false, 'message' => 'Missing message']];
            }

            try {
                $error['service'] = OpenTelemetry::AREA_FRONTEND;
                $this->logger->error($error['message'], $error);
            } catch (\Exception $e) {
                return [['success' => false, 'message' => $e->getMessage()]];
            }

            return [['success' => true, 'message' => 'Log saved']];
        }
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
