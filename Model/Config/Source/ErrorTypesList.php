<?php

namespace OuterEdge\Opentelemetry\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ErrorTypesList implements ArrayInterface
{
    //ToDo check magento core to find a way to get all error types availabel
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['label' => 'exception', 'value' => 'EXCEPTION'];
        $options[] = ['label' => 'error', 'value' => 'ERROR'];
        $options[] = ['label' => 'critical', 'value' => 'CRITICAL'];
        return $options;
    }
}