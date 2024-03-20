<?php

namespace OuterEdge\OpenTelemetry\ExclusionList;

interface ExclusionListInterface
{
    public static function getPatterns(): array;
}
