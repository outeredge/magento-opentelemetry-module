<?php

namespace OuterEdge\OpenTelemetry\ExclusionList;

class Common implements ExclusionListInterface
{
    public static function getPatterns(): array
    {
        return [
            'Can not resolve reCAPTCHA parameter',
            'maintenance mode is enabled',
        ];
    }
}
