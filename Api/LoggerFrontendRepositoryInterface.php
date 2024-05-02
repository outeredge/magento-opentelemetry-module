<?php

namespace OuterEdge\OpenTelemetry\Api;

interface LoggerFrontendRepositoryInterface
{
    /**
     * @param mixed $errors
     * @return array
     */
    public function log($errors);
}
