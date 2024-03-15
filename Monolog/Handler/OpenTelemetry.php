<?php

namespace OuterEdge\OpenTelemetry\Monolog\Handler;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use OpenTelemetry\Contrib\Logs\Monolog\Handler;
use OpenTelemetry\Contrib\Otlp\ContentTypes;
use OpenTelemetry\Contrib\Otlp\HttpEndpointResolverInterface;
use OpenTelemetry\Contrib\Otlp\LogsExporter;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Logs\LoggerProvider;
use OpenTelemetry\SDK\Logs\Processor\SimpleLogRecordProcessor;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SemConv\ResourceAttributes;

class OpenTelemetry extends Handler
{
    const CONFIG_KEY_HEADERS  = 'oe_open_telemetry/settings/headers';
    const CONFIG_KEY_ENABLED  = 'oe_open_telemetry/settings/enable';
    const CONFIG_KEY_SERVICE  = 'oe_open_telemetry/settings/service';
    const CONFIG_KEY_ENDPOINT = 'oe_open_telemetry/settings/endpoint';
    const CONFIG_KEY_LOGLEVEL = 'oe_open_telemetry/settings/log_level';

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        State $appState,
        ProductMetadataInterface $productMetadata
    ) {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $areaCode = $appState->getAreaCode();
        } catch (LocalizedException $ex) {
            $areaCode = 'unknown';
        }

        $resource = ResourceInfoFactory::emptyResource()->merge(ResourceInfo::create(Attributes::create([
            ResourceAttributes::SERVICE_NAMESPACE => $areaCode,
            ResourceAttributes::SERVICE_NAME => $this->scopeConfig->getValue(self::CONFIG_KEY_SERVICE),
            ResourceAttributes::SERVICE_VERSION => $productMetadata->getVersion(),
            ResourceAttributes::DEPLOYMENT_ENVIRONMENT => $appState->getMode()
        ])));

        $transport = (new OtlpHttpTransportFactory())->create(
            $this->scopeConfig->getValue(self::CONFIG_KEY_ENDPOINT) . '/' . HttpEndpointResolverInterface::LOGS_DEFAULT_PATH,
            ContentTypes::PROTOBUF,
            $this->getHeaders()
        );

        $loggerProvider = LoggerProvider::builder()
            ->setResource($resource)
            ->addLogRecordProcessor(new SimpleLogRecordProcessor(new LogsExporter($transport)))
            ->build();

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

        parent::write($record);
    }

    private function getHeaders(): array
    {
        $headers = [];
        if (!empty($headerconfig = $this->scopeConfig->getValue(self::CONFIG_KEY_HEADERS))) {
            $headerconfig = explode(',', $headerconfig);
            foreach ($headerconfig as $header) {
                list($k, $v) = explode('=', $header);
                $headers[$k] = $v;
            }
        }
        return $headers;
    }

    private function isEnabled()
    {
        return (bool) $this->scopeConfig->isSetFlag(self::CONFIG_KEY_ENABLED);
    }
}
