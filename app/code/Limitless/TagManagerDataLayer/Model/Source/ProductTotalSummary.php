<?php

namespace Limitless\TagManagerDataLayer\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class ProductTotalSummary implements ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'individual',
                'label' => __('Individual')
            ),
            array(
                'value' => 'total',
                'label' => __('Line Total')
            )
        );
    }
}