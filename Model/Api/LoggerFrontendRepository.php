<?php

namespace OuterEdge\OpenTelemetry\Model\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Webapi\Exception;
use OuterEdge\OpenTelemetry\Api\LoggerFrontendRepositoryInterface;
use Psr\Log\LoggerInterface;

class LoggerFrontendRepository implements LoggerFrontendRepositoryInterface
{
    const CONFIG_KEY_ENABLE_FRONTEND = 'oe_open_telemetry/settings/enable_frontend';

    protected ?bool $enabled = null;

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected LoggerInterface $logger,
        protected Request $request
    ) {
    }

    /**
     * @inheritdoc
     */
    public function log($errors)
    {
        if (!$this->isEnabled()) {
            throw new Exception(new Phrase('Frontend logging is disabled'));
        }

        if (!$this->request->isXmlHttpRequest()) {
            throw new Exception(new Phrase('Forbidden'));
        }

        foreach ($errors as $error) {
            foreach (['message', 'type'] as $key) {
                if (empty($error[$key])) {
                    throw new Exception(new Phrase("Missing or empty `$key` value"));
                }
            }

            // For now, we send everything as an "error" type
            $this->logger->error($error['message']);
        }

        return [['success' => true]];
    }

    protected function isEnabled(): bool
    {
        if (null === $this->enabled) {
            $this->enabled = $this->scopeConfig->getValue(self::CONFIG_KEY_ENABLE_FRONTEND);
        }

        return $this->enabled;
    }
}
