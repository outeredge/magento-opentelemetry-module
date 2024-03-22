<?php

namespace OuterEdge\OpenTelemetry\Logs;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use OpenTelemetry\API\Logs\LoggerProviderInterface;
use OpenTelemetry\API\Logs\LoggerInterface;
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
use OuterEdge\OpenTelemetry\Monolog\Handler\OpenTelemetry as Handler;

class LazyLoggerProvider implements LoggerProviderInterface
{
    protected ?LoggerProviderInterface $loggerProvider = null;

    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected State $appState,
        protected StoreManagerInterface $storeManager,
        protected ProductMetadataInterface $productMetadata
    ) {
    }

    public function getLogger(
        string $name,
        ?string $version = null,
        ?string $schemaUrl = null,
        iterable $attributes = []
    ): LoggerInterface
    {
        if (null === $this->loggerProvider) {
            try {
                $areaCode = $this->appState->getAreaCode();
            } catch (LocalizedException $ex) {
                $areaCode = 'unknown';
            }

            $service = $this->scopeConfig->getValue(Handler::CONFIG_KEY_SERVICE);
            if (empty($service)) {
                $service = $this->storeManager->getStore()->getBaseUrl();
            }

            $resource = ResourceInfoFactory::emptyResource()->merge(ResourceInfo::create(Attributes::create([
                ResourceAttributes::SERVICE_NAMESPACE => $areaCode,
                ResourceAttributes::SERVICE_NAME => $service,
                ResourceAttributes::SERVICE_VERSION => $this->productMetadata->getVersion(),
                ResourceAttributes::DEPLOYMENT_ENVIRONMENT => $this->appState->getMode(),
            ])));

            $transport = (new OtlpHttpTransportFactory())->create(
                $this->scopeConfig->getValue(Handler::CONFIG_KEY_ENDPOINT) . '/' . HttpEndpointResolverInterface::LOGS_DEFAULT_PATH,
                ContentTypes::PROTOBUF,
                $this->getHeaders(),
                null,
                3.
            );

            $this->loggerProvider = LoggerProvider::builder()
                ->setResource($resource)
                ->addLogRecordProcessor(new SimpleLogRecordProcessor(new LogsExporter($transport)))
                ->build();
        }

        return $this->loggerProvider->getLogger($name, $version, $schemaUrl, $attributes);
    }

    protected function getHeaders(): array
    {
        $headers = [];
        if (!empty($headerconfig = $this->scopeConfig->getValue(Handler::CONFIG_KEY_HEADERS))) {
            $headerconfig = explode(',', $headerconfig);
            foreach ($headerconfig as $header) {
                list($k, $v) = explode('=', $header);
                $headers[$k] = $v;
            }
        }
        return $headers;
    }
}
