<?php

namespace Limitless\ProductPDF\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class PDFSecurityUrl implements ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'unsecure',
                'label' => __('Unsecure')
            ),
            array(
                'value' => 'secure',
                'label' => __('Secure')
            )
        );
    }
}