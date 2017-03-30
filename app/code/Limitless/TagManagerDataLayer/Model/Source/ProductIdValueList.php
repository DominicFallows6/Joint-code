<?php

namespace Limitless\TagManagerDataLayer\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class ProductIdValueList implements ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'sku',
                'label' => __('Product SKU')
            ),
            array(
                'value' => 'id',
                'label' => __('Product ID')
            )
        );
    }
}