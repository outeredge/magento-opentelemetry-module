<?php

namespace OuterEdge\OpenTelemetry\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Monolog\Logger;

class ErrorTypesList implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $return = array();
        foreach (Logger::getLevels() as $key => $value) {
            $return[] = array(
                'value' => $key,
                'label' => $key
            );
        }
        return $return;
    }
}
