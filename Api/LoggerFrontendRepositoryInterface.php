<?php

namespace OuterEdge\OpenTelemetry\Api;

interface LoggerFrontendRepositoryInterface
{
    /**
     * @param mixed $message
     * @return array
     */
    public function log($message);
}