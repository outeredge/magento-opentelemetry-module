<?php

namespace OuterEdge\OpenTelemetry;

use Monolog\Logger;

class LoggerTest extends Logger
{

    public function setHandlers(array $handlers): self
    {
        $this->handlers = [];
        foreach (array_reverse($handlers) as $handler) {

            if ($handler instanceof \OuterEdge\OpenTelemetry\Monolog\Handler\OpenTelemetry) {
                $this->pushHandler(new \Monolog\Handler\DeduplicationHandler($handler, null, Logger::ERROR, 60, true));
            } else {
                $this->pushHandler($handler);
            }
        }

        return $this;
    }
}
