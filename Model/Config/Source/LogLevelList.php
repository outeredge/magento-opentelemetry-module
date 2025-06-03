<?php

namespace OuterEdge\OpenTelemetry\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Monolog\Level;

class LogLevelList implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $return = [];
        $levels = array_combine(Level::NAMES, Level::VALUES);
        foreach ($levels as $key => $value) {
            $return[] = array(
                'value' => $key,
                'label' => $key
            );
        }
        return $return;
    }
}
