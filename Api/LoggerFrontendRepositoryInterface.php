<?php

namespace OuterEdge\OpenTelemetry\Api;

interface LoggerFrontendRepositoryInterface
{
    /**
     * @param mixed $errors
     * @return mixed[]
     */
    public function log($errors);
}
