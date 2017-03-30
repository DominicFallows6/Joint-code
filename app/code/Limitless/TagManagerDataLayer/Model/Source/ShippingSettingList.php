<?php

namespace Limitless\TagManagerDataLayer\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class ShippingSettingList implements ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'include',
                'label' => __('Include Shipping')
            ),
            array(
                'value' => 'exclude',
                'label' => __('Exclude Shipping')
            )
        );
    }
}