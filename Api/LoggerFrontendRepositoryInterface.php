<?php

namespace OuterEdge\OpenTelemetry\Api;

interface LoggerFrontendRepositoryInterface
{
    /**
     * @param mixed $data
     * @return array
     */
    public function setLog($data);
}

