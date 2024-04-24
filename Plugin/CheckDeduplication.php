<?php

namespace OuterEdge\OpenTelemetry\Plugin;

use Monolog\Logger;
use Monolog\Handler\DeduplicationHandler;

class CheckDeduplication
{
     /**
     * @var LeadsHelper
     */
    protected $leadsHelper;

    /**
     * @param LeadsHelper $leadsHelper
     */
    public function __construct(
        protected DeduplicationHandler $deduplicationHandler
    ) {
    }

    public function afterPushHandler(Logger $subject, $handler)
	{
        if ($handler instanceof \OuterEdge\OpenTelemetry\Monolog\Handler\OpenTelemetry) {
           $test = new \Monolog\Handler\DeduplicationHandler($handler, null, Logger::ERROR, 60, true);
           $test->flush();
        }
    }
}
