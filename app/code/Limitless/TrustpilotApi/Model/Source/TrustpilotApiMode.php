<?php

namespace Limitless\TrustpilotApi\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class TrustpilotApiMode implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return
            [
                [
                    'value' => 'ajax',
                    'label' => 'Ajax'
                ],
                [
                    'value' => 'cache',
                    'label' => 'Cache'
                ]
            ];
    }
}