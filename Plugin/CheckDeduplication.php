<?php

namespace OuterEdge\OpenTelemetry\Plugin;

use Monolog\Logger;
use Monolog\Handler\DeduplicationHandler;

class CheckDeduplication
{
    public function afterPushHandler(Logger $subject, $result, $handler)
	{
        if ($handler instanceof \OuterEdge\OpenTelemetry\Monolog\Handler\OpenTelemetry) {
            $openTelemetry = new \Monolog\Handler\DeduplicationHandler($handler, null, Logger::ERROR, 60, true);
            $openTelemetry->flush();
        }

        return $subject;
    }
}
