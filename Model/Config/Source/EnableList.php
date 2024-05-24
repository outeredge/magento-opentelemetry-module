<?php

namespace OuterEdge\OpenTelemetry\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class EnableList implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'all',
                'label' => __('Enable')
            ],
            [
                'value' => 'checkout',
                'label' => __('Enable in Checkout Only')
            ],
            [
                'value' => 'disable',
                'label' => __('Disable')
            ]
        ];
    }
}
