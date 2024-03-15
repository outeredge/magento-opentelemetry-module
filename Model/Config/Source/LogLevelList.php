<?php

namespace OuterEdge\OpenTelemetry\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Monolog\Logger;

class LogLevelList implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $return = [];
        foreach (Logger::getLevels() as $key => $value) {
            $return[] = array(
                'value' => $key,
                'label' => $key
            );
        }
        return $return;
    }
}
