<?php

namespace OuterEdge\Opentelemetry\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Monolog\Logger;

class ErrorTypesList implements ArrayInterface
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
