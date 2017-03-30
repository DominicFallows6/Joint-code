<?php

namespace Limitless\TagManagerDataLayer\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class MaxProductsToDisplay implements ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => __('1')
            ),
            array(
                'value' => '2',
                'label' => __('2')
            ),
            array(
                'value' => '3',
                'label' => __('3')
            ),
            array(
                'value' => '4',
                'label' => __('4')
            ),
            array(
                'value' => '5',
                'label' => __('5')
            )
        );
    }
}