<?php

namespace OuterEdge\OpenTelemetry\Logger\Handler;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Exception as ExceptionHandler;
use Magento\Framework\Logger\Handler\Base;
use OpenTelemetry\SDK\Logs\LoggerProvider;
use OpenTelemetry\Contrib\Otlp\LogsExporterFactory;
use OpenTelemetry\API\Logs\EventLogger;
use OpenTelemetry\API\Logs\LogRecord;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Logs\Processor\SimpleLogRecordProcessor;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class OpenTelemetry extends Base
{
    public function __construct(
        DriverInterface $filesystem,
        protected ExceptionHandler $exceptionHandler,
        protected StoreManagerInterface $storeManager,
        protected ProductMetadataInterface $productMetadata,
        protected ScopeConfigInterface $scopeConfig,
        ?string $filePath = null
    ) {
        if ($this->isEnabled()) {
            $endpoint = $this->scopeConfig->getValue('oe_open_telemetry/settings/endpoint');
            $headers  = $this->scopeConfig->getValue('oe_open_telemetry/settings/headers');

            putenv('OTEL_PHP_AUTOLOAD_ENABLED=true');
            putenv('OTEL_METRICS_EXPORTER=none');
            putenv('OTEL_LOGS_EXPORTER=otlp');
            putenv('OTEL_LOGS_PROCESSOR=batch');
            putenv('OTEL_EXPORTER_OTLP_PROTOCOL=http/protobuf');
            putenv('OTEL_EXPORTER_OTLP_ENDPOINT=' . $endpoint);

            if (!empty($headers)) {
                putenv('OTEL_EXPORTER_OTLP_HEADERS=' . $headers);
            }
        }

        parent::__construct($filesystem, $filePath);
    }

    /**
     * Writes formatted record through the handler
     *
     * @param array $record The record metadata
     * @return void
     */
    public function write(array $record): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $typesToLog = explode(',', $this->scopeConfig->getValue('oe_open_telemetry/settings/log_types'));

        if (in_array($record['level_name'], $typesToLog)) {
            $storeName = $this->storeManager->getWebsite()->getCode();
            $magentoVersion = $this->productMetadata->getVersion();
            $domain = $this->storeManager->getStore()->getBaseUrl();

            $loggerProvider = LoggerProvider::builder()
                ->addLogRecordProcessor(
                    new SimpleLogRecordProcessor(
                        (new LogsExporterFactory())->create()
                    )
                )
                ->setResource(ResourceInfo::create(Attributes::create(['message' => $record['formatted']])))
                ->build();

            $logger = $loggerProvider->getLogger($storeName, $magentoVersion, $domain, []);
            $eventLogger = new EventLogger($logger, $domain);

            $recordLog = (new LogRecord([]))
                ->setSeverityText($record['level_name'])
                ->setSeverityNumber($record['level']);

            $eventLogger->logEvent($record['level_name'], $recordLog);
            $loggerProvider->shutdown();
        }
    }

    private function isEnabled()
    {
        return (bool) $this->scopeConfig->isSetFlag('oe_open_telemetry/settings/enable');
    }
}
