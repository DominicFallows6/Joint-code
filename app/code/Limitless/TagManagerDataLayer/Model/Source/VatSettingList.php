<?php

namespace Limitless\TagManagerDataLayer\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class VatSettingList implements ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'include',
                'label' => __('Include VAT')
            ),
            array(
                'value' => 'exclude',
                'label' => __('Exclude VAT')
            )
        );
    }
}