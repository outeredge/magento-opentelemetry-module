<?php

namespace OuterEdge\OpenTelemetry\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ExclusionList implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $return = [];

        $return[] = [
            'value' => 'common',
            'label' => 'Common',
        ];

        return $return;
    }
}
